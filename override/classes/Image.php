<?php
/**
* Scaleflex DAM - Image Override
*/

class Image extends ImageCore
{
    public $url;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition['fields']['url'] = ['type' => self::TYPE_STRING];
        
        parent::__construct($id, $id_lang, $id_shop);
    }

    /**
     * Override to bypass physical file deletion if it's a DAM image
     */
    public function deleteImage($forceDelete = false)
    {
        // If image has a DAM url, there are no physical files to delete on the server
        if ($this->url && !empty($this->url)) {
            return true;
        }

        return parent::deleteImage($forceDelete);
    }
}
