<?php

class MetodosAPI
{
    private $API_KEY;
    private $TOKEN_ACCESS;
    private $HOST_NAME;

    public function __construct()
    {
        $this->API_KEY = env('SHOPIFY_API_KEY');
        $this->TOKEN_ACCESS = env('SHOPIFY_TOKEN_ACCESS');
        $this->HOST_NAME = env('SHOPIFY_HOST_NAME');
    }

    public function API()
    {
        $URL_REST_SHOPIFY = "https://" . $this->API_KEY . ":" . $this->TOKEN_ACCESS . "@" . $this->HOST_NAME;

        header('Content-Type: application/JSON');
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET': //consulta
                $this->MetodoGet($URL_REST_SHOPIFY);
                break;
            case 'POST': //inserta
                $this->MetodoPost($URL_REST_SHOPIFY);
                break;
            case 'PUT': //actualiza
                $this->MetodoPut($URL_REST_SHOPIFY);
                break;
            case 'DELETE': //elimina
                $this->MetodoDelete($URL_REST_SHOPIFY);
                break;
            default: //metodo NO soportado
                echo 'METODO NO SOPORTADO';
                break;
        }
    }


    function response($code = 200, $status = "", $message = "")
    {
        http_response_code($code);
        if (!empty($status) && !empty($message)) {
            $response = array("status" => $status, "message" => $message);
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    function MetodoPost($URL_REST_SHOPIFY)
    {
        $obj = json_decode(file_get_contents('php://input'));
        $objArr = (array)$obj;

        // Validar que se recibió JSON válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(400, "error", "JSON inválido");
            return;
        }

        // Permitir crear producto sin parámetro action (estilo Shopify REST)
        if (!isset($_GET['action']) || empty($_GET['action']) || $_GET['action'] == 'createProduct') {
            // Validar datos requeridos
            if (empty($objArr['title']) || empty($objArr['body_html']) || empty($objArr['product_type'])) {
                $this->response(422, "error", "Faltan campos requeridos: title, body_html, product_type");
                return;
            }

            // Manejo de variantes y opciones
            $variant = array(
                "price" => $objArr['price'] ?? "0.00",
                "inventory_quantity" => $objArr['inventory_quantity'] ?? 0,
                "sku" => $objArr['sku'] ?? "",
                "inventory_management" => "shopify"
            );
            // Si se recibe option1 (ej: talla)
            if (!empty($objArr['option1'])) {
                $variant['option1'] = $objArr['option1'];
            }

            $productData = array(
                "product" => array(
                    "title" => $objArr['title'],
                    "body_html" => $objArr['body_html'],
                    "vendor" => $objArr['vendor'] ?? "API Store",
                    "product_type" => $objArr['product_type'],
                    "status" => $objArr['status'] ?? "active",
                    "variants" => array($variant)
                )
            );
            // Opciones (ej: talla)
            if (!empty($objArr['options']) && is_array($objArr['options'])) {
                $productData['product']['options'] = $objArr['options'];
            }

            // Agregar imágenes si se proporcionan (URL o base64)
            if (!empty($objArr['images']) && is_array($objArr['images'])) {
                $images = array();
                foreach ($objArr['images'] as $img) {
                    // Si es string, asumimos que es URL
                    if (is_string($img) && filter_var($img, FILTER_VALIDATE_URL)) {
                        $images[] = array("src" => $img);
                    }
                    // Si es array y tiene src o attachment
                    elseif (is_array($img)) {
                        if (isset($img['src']) && filter_var($img['src'], FILTER_VALIDATE_URL)) {
                            $images[] = array("src" => $img['src']);
                        } elseif (isset($img['attachment'])) {
                            $images[] = array("attachment" => $img['attachment']);
                        }
                    }
                }
                if (!empty($images)) {
                    $productData['product']['images'] = $images;
                }
            }

            $response = callAPI('POST', $URL_REST_SHOPIFY . '/admin/api/2023-10/products.json', json_encode($productData));
            $JSON_response = json_decode($response, true);

            if (isset($JSON_response['product'])) {
                echo json_encode(array(
                    "ok" => true,
                    "message" => "Producto creado exitosamente",
                    "product" => $JSON_response['product']
                ));
            } else {
                $this->response(500, "error", "Error al crear el producto");
            }
            return;
        }

        // Agregar imagen a producto existente (desde URL)
        if ($_GET['action'] == 'addProductImage') {
            if (empty($_GET['product_id']) || empty($objArr['image_url'])) {
                $this->response(422, "error", "Se requiere product_id y image_url");
                return;
            }

            $productId = $_GET['product_id'];

            // Validar que la URL de imagen sea válida
            if (!filter_var($objArr['image_url'], FILTER_VALIDATE_URL)) {
                $this->response(422, "error", "URL de imagen no válida");
                return;
            }

            $imageData = array(
                "image" => array(
                    "src" => $objArr['image_url'],
                    "alt" => $objArr['alt_text'] ?? ""
                )
            );

            $response = callAPI('POST', $URL_REST_SHOPIFY . '/admin/api/2023-10/products/' . $productId . '/images.json', json_encode($imageData));
            $JSON_response = json_decode($response, true);

            if (isset($JSON_response['image'])) {
                echo json_encode(array(
                    "ok" => true,
                    "message" => "Imagen agregada exitosamente",
                    "image" => $JSON_response['image']
                ));
            } else {
                $this->response(500, "error", "Error al agregar la imagen");
            }
            return;
        }

        // Agregar imagen a producto existente (desde archivo Base64)
        if ($_GET['action'] == 'uploadProductImage') {
            if (empty($_GET['product_id']) || empty($objArr['image_base64'])) {
                $this->response(422, "error", "Se requiere product_id y image_base64");
                return;
            }

            $productId = $_GET['product_id'];
            $imageBase64 = $objArr['image_base64'];

            // Validar formato Base64
            if (!preg_match('/^data:image\/(jpeg|jpg|png|gif);base64,/', $imageBase64)) {
                $this->response(422, "error", "Formato de imagen Base64 no válido. Use: data:image/[jpeg|jpg|png|gif];base64,");
                return;
            }

            $imageData = array(
                "image" => array(
                    "attachment" => $imageBase64,
                    "alt" => $objArr['alt_text'] ?? "",
                    "filename" => $objArr['filename'] ?? "uploaded_image.jpg"
                )
            );

            $response = callAPI('POST', $URL_REST_SHOPIFY . '/admin/api/2023-10/products/' . $productId . '/images.json', json_encode($imageData));
            $JSON_response = json_decode($response, true);

            if (isset($JSON_response['image'])) {
                echo json_encode(array(
                    "ok" => true,
                    "message" => "Imagen subida exitosamente",
                    "image" => $JSON_response['image']
                ));
            } else {
                $this->response(500, "error", "Error al subir la imagen");
            }
            return;
        }

        // Crear descuento
        if ($_GET['action'] == 'createDiscount') {
            if (empty($objArr['code']) || empty($objArr['discount_type']) || empty($objArr['value'])) {
                $this->response(422, "error", "Faltan campos requeridos: code, discount_type, value");
                return;
            }

            $discountData = array(
                "discount_code" => array(
                    "code" => $objArr['code'],
                    "discount_type" => $objArr['discount_type'], // "percentage" o "fixed_amount"
                    "value" => $objArr['value'],
                    "usage_limit" => $objArr['usage_limit'] ?? null
                )
            );

            $response = callAPI('POST', $URL_REST_SHOPIFY . '/admin/api/2023-10/discount_codes.json', json_encode($discountData));
            $JSON_response = json_decode($response, true);

            if (isset($JSON_response['discount_code'])) {
                echo json_encode(array(
                    "ok" => true,
                    "message" => "Código de descuento creado exitosamente",
                    "discount" => $JSON_response['discount_code']
                ));
            } else {
                $this->response(500, "error", "Error al crear el código de descuento");
            }
            return;
        }

        $this->response(400, "error", "Acción no válida para POST");
    }

    function MetodoPut($URL_REST_SHOPIFY)
    {
        $obj = json_decode(file_get_contents('php://input'));
        $objArr = (array)$obj;

        // Validar que se recibió JSON válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(400, "error", "JSON inválido");
            return;
        }

        // Verificar que se proporcione el parámetro action
        if (!isset($_GET['action'])) {
            $this->response(400, "error", "Se requiere el parámetro 'action'");
            return;
        }

        // Actualizar producto
        if ($_GET['action'] == 'updateProduct') {
            // Validar que se proporcione el ID del producto
            if (empty($_GET['id'])) {
                $this->response(422, "error", "Se requiere el ID del producto");
                return;
            }

            $productId = $_GET['id'];
            $updateData = array("product" => array());

            // Campos que se pueden actualizar
            $allowedFields = ['title', 'body_html', 'vendor', 'product_type', 'status'];
            foreach ($allowedFields as $field) {
                if (isset($objArr[$field])) {
                    $updateData['product'][$field] = $objArr[$field];
                }
            }

            // Actualizar variantes si se proporcionan
            if (isset($objArr['variants']) && is_array($objArr['variants'])) {
                $updateData['product']['variants'] = $objArr['variants'];
            }

            if (empty($updateData['product'])) {
                $this->response(422, "error", "No se proporcionaron campos para actualizar");
                return;
            }

            $response = callAPI('PUT', $URL_REST_SHOPIFY . '/admin/api/2023-10/products/' . $productId . '.json', json_encode($updateData));
            $JSON_response = json_decode($response, true);

            if (isset($JSON_response['product'])) {
                echo json_encode(array(
                    "ok" => true,
                    "message" => "Producto actualizado exitosamente",
                    "product" => $JSON_response['product']
                ));
            } else {
                $this->response(500, "error", "Error al actualizar el producto");
            }
            return;
        }

        // Actualizar inventario
        if ($_GET['action'] == 'updateInventory') {
            if (empty($_GET['variant_id']) || !isset($objArr['quantity'])) {
                $this->response(422, "error", "Se requiere variant_id y quantity");
                return;
            }

            $variantId = $_GET['variant_id'];
            $inventoryData = array(
                "variant" => array(
                    "id" => $variantId,
                    "inventory_quantity" => intval($objArr['quantity'])
                )
            );

            $response = callAPI('PUT', $URL_REST_SHOPIFY . '/admin/api/2023-10/variants/' . $variantId . '.json', json_encode($inventoryData));
            $JSON_response = json_decode($response, true);

            if (isset($JSON_response['variant'])) {
                echo json_encode(array(
                    "ok" => true,
                    "message" => "Inventario actualizado exitosamente",
                    "variant" => $JSON_response['variant']
                ));
            } else {
                $this->response(500, "error", "Error al actualizar el inventario");
            }
            return;
        }

        $this->response(400, "error", "Acción no válida para PUT");
    }

    function MetodoDelete($URL_REST_SHOPIFY)
    {
        // Verificar que se proporcione el parámetro action
        if (!isset($_GET['action'])) {
            $this->response(400, "error", "Se requiere el parámetro 'action'");
            return;
        }

        // Eliminar producto
        if ($_GET['action'] == 'deleteProduct') {
            if (empty($_GET['id'])) {
                $this->response(422, "error", "Se requiere el ID del producto");
                return;
            }

            $productId = $_GET['id'];
            $response = callAPI('DELETE', $URL_REST_SHOPIFY . '/admin/api/2023-10/products/' . $productId . '.json', false);

            // Shopify retorna 200 con un objeto vacío al eliminar exitosamente
            if ($response === '{}' || empty($response)) {
                echo json_encode(array(
                    "ok" => true,
                    "message" => "Producto eliminado exitosamente",
                    "product_id" => $productId
                ));
            } else {
                $JSON_response = json_decode($response, true);
                if (isset($JSON_response['errors'])) {
                    $this->response(400, "error", "Error al eliminar: " . json_encode($JSON_response['errors']));
                } else {
                    $this->response(500, "error", "Error desconocido al eliminar el producto");
                }
            }
            return;
        }

        // Eliminar variante de producto
        if ($_GET['action'] == 'deleteVariant') {
            if (empty($_GET['product_id']) || empty($_GET['variant_id'])) {
                $this->response(422, "error", "Se requiere product_id y variant_id");
                return;
            }

            $productId = $_GET['product_id'];
            $variantId = $_GET['variant_id'];

            $response = callAPI('DELETE', $URL_REST_SHOPIFY . '/admin/api/2023-10/products/' . $productId . '/variants/' . $variantId . '.json', false);

            if ($response === '{}' || empty($response)) {
                echo json_encode(array(
                    "ok" => true,
                    "message" => "Variante eliminada exitosamente",
                    "variant_id" => $variantId
                ));
            } else {
                $JSON_response = json_decode($response, true);
                if (isset($JSON_response['errors'])) {
                    $this->response(400, "error", "Error al eliminar variante: " . json_encode($JSON_response['errors']));
                } else {
                    $this->response(500, "error", "Error desconocido al eliminar la variante");
                }
            }
            return;
        }

        $this->response(400, "error", "Acción no válida para DELETE");
    }

    function MetodoGet($URL_REST_SHOPIFY)
    {

        $obj = json_decode(file_get_contents('php://input'));
        $objArr = (array)$obj;

        // Si no hay parámetro action o está vacío, usar rutas estándar de Shopify
        if (!isset($_GET['action']) || empty($_GET['action'])) {

            // Si hay un ID específico en la URL, obtener ese producto
            if (isset($_GET['id'])) {
                $productId = $_GET['id'];
                $getDataShopify = callAPI('GET', $URL_REST_SHOPIFY . '/admin/api/2023-10/products/' . $productId . '.json', false);
                $JSON_getDataShopify = json_decode($getDataShopify, true);

                if (isset($JSON_getDataShopify['product'])) {
                    echo json_encode(
                        array(
                            "ok" => true,
                            "message" => 'product',
                            "product" => $JSON_getDataShopify['product'],
                        )
                    );
                } else {
                    $this->response(404, "error", "Producto no encontrado");
                }
                return;
            }

            // Obtener productos (ruta estándar de Shopify)
            $url = $URL_REST_SHOPIFY . '/admin/api/2023-10/products.json';
            $params = array();

            // Parámetros opcionales
            if (isset($_GET['ids'])) {
                $params[] = 'ids=' . urlencode($_GET['ids']);
            }
            if (isset($_GET['limit'])) {
                $limit = min(intval($_GET['limit']), 250); // Shopify límite máximo
                $params[] = 'limit=' . $limit;
            } else {
                $params[] = 'limit=50'; // Límite por defecto
            }
            if (isset($_GET['status'])) {
                $params[] = 'status=' . urlencode($_GET['status']);
            }
            if (isset($_GET['product_type'])) {
                $params[] = 'product_type=' . urlencode($_GET['product_type']);
            }
            if (isset($_GET['vendor'])) {
                $params[] = 'vendor=' . urlencode($_GET['vendor']);
            }
            if (isset($_GET['since_id'])) {
                $params[] = 'since_id=' . urlencode($_GET['since_id']);
            }
            if (isset($_GET['created_at_min'])) {
                $params[] = 'created_at_min=' . urlencode($_GET['created_at_min']);
            }
            if (isset($_GET['created_at_max'])) {
                $params[] = 'created_at_max=' . urlencode($_GET['created_at_max']);
            }
            if (isset($_GET['updated_at_min'])) {
                $params[] = 'updated_at_min=' . urlencode($_GET['updated_at_min']);
            }
            if (isset($_GET['updated_at_max'])) {
                $params[] = 'updated_at_max=' . urlencode($_GET['updated_at_max']);
            }
            if (isset($_GET['fields'])) {
                $params[] = 'fields=' . urlencode($_GET['fields']);
            }

            if (!empty($params)) {
                $url .= '?' . implode('&', $params);
            }

            $getDataShopify = callAPI('GET', $url, false);
            $JSON_getDataShopify = json_decode($getDataShopify, true);

            if (isset($JSON_getDataShopify['products'])) {
                echo json_encode(
                    array(
                        "ok" => true,
                        "message" => 'products',
                        "count" => count($JSON_getDataShopify['products']),
                        "products" => $JSON_getDataShopify['products'],
                    )
                );
            } else {
                $this->response(500, "error", "Error al obtener productos");
            }
            return;
        }

        if ($_GET['action'] == 'getAllProducts') {
            // Parámetros opcionales para paginación y filtros
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50; // Máximo 250
            $limit = min($limit, 250); // Shopify límite máximo

            $url = $URL_REST_SHOPIFY . '/admin/api/2023-10/products.json?limit=' . $limit;

            // Filtros opcionales
            if (isset($_GET['status'])) {
                $url .= '&status=' . urlencode($_GET['status']);
            }
            if (isset($_GET['product_type'])) {
                $url .= '&product_type=' . urlencode($_GET['product_type']);
            }
            if (isset($_GET['vendor'])) {
                $url .= '&vendor=' . urlencode($_GET['vendor']);
            }

            $getDataShopify = callAPI('GET', $url, false);
            $JSON_getDataShopify = json_decode($getDataShopify, true);

            if (isset($JSON_getDataShopify['products'])) {
                echo json_encode(
                    array(
                        "ok" => true,
                        "message" => 'getAllProducts',
                        "count" => count($JSON_getDataShopify['products']),
                        "products" => $JSON_getDataShopify['products'],
                    )
                );
            } else {
                $this->response(500, "error", "Error al obtener productos");
            }
            return;
        }
        if ($_GET['action'] == 'getProductById') {
            // Validar que se proporcione el ID del producto
            if (empty($_GET['id'])) {
                $this->response(422, "error", "Se requiere el ID del producto");
                return;
            }

            $productId = $_GET['id'];
            $getDataShopify = callAPI('GET', $URL_REST_SHOPIFY . '/admin/api/2023-10/products/' . $productId . '.json', false);
            $JSON_getDataShopify = json_decode($getDataShopify, true);

            if (isset($JSON_getDataShopify['product'])) {
                echo json_encode(
                    array(
                        "ok" => true,
                        "message" => 'getProductById',
                        "product" => $JSON_getDataShopify['product'],
                    )
                );
            } else {
                $this->response(404, "error", "Producto no encontrado");
            }
            return;
        }

        if ($_GET['action'] == 'getAllDiscounts') {
            // Obtener el conteo de códigos de descuento
            $getDataShopify = callAPI('GET', $URL_REST_SHOPIFY . '/admin/api/2023-10/discount_codes/count.json', false);
            $JSON_getDataShopify = json_decode($getDataShopify, true);

            echo json_encode(
                array(
                    "ok" => true,
                    "message" => 'getAllDiscounts',
                    "discount_count" => $JSON_getDataShopify,
                )
            );
            return;
        }

        // Obtener inventario de producto
        if ($_GET['action'] == 'getInventory') {
            if (empty($_GET['product_id'])) {
                $this->response(422, "error", "Se requiere el ID del producto");
                return;
            }

            $productId = $_GET['product_id'];
            $getDataShopify = callAPI('GET', $URL_REST_SHOPIFY . '/admin/api/2023-10/products/' . $productId . '/variants.json', false);
            $JSON_getDataShopify = json_decode($getDataShopify, true);

            if (isset($JSON_getDataShopify['variants'])) {
                echo json_encode(
                    array(
                        "ok" => true,
                        "message" => 'getInventory',
                        "product_id" => $productId,
                        "variants" => $JSON_getDataShopify['variants'],
                    )
                );
            } else {
                $this->response(404, "error", "Variantes no encontradas");
            }
            return;
        }

        $this->response(400, "error", "Acción no válida para GET");
    }
}//end class