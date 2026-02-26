<?php
/**
 * 2007-2026 PrestaShop
 *
 * NOTICE OF LICENSE
 * ...
 */

class ScaleflexDamProxyModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $id_image = (int) Tools::getValue('id_image');

        if ($id_image) {
            $url = Db::getInstance()->getValue('SELECT `url` FROM `'._DB_PREFIX_.'image` WHERE `id_image` = ' . $id_image);
            
            if ($url && !empty($url)) {
                $context = stream_context_create([
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ],
                    "http" => [
                        "user_agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) PrestaShop Proxy",
                        "ignore_errors" => true
                    ]
                ]);
                
                $content = @file_get_contents($url, false, $context);
                $status = 200;
                if (isset($http_response_header) && is_array($http_response_header)) {
                    preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $match);
                    if (isset($match[1])) {
                        $status = (int)$match[1];
                    }
                }
                
                if ($content === false || $status != 200) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) PrestaShop Proxy');
                    $content = curl_exec($ch);
                    curl_close($ch);
                }
                
                if ($content !== false && !empty($content)) {
                    $info = @getimagesizefromstring($content);
                    if ($info && isset($info['mime'])) {
                        header('Content-Type: ' . $info['mime']);
                    } else {
                        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                        $mime = 'image/jpeg';
                        if ($ext === 'png') $mime = 'image/png';
                        elseif ($ext === 'gif') $mime = 'image/gif';
                        elseif ($ext === 'webp') $mime = 'image/webp';
                        elseif ($ext === 'svg') $mime = 'image/svg+xml';
                        header('Content-Type: ' . $mime);
                    }
                    
                    header('Cache-Control: max-age=86400, public');
                    header('Access-Control-Allow-Origin: *');
                    
                    echo $content;
                    exit;
                }
            }
        }

        header("HTTP/1.0 404 Not Found");
        exit;
    }
}
