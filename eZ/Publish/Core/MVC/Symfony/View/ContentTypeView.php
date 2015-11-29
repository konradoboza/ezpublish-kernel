<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\View;

/**
 * A View of a ContentType
 * @package eZ\Publish\Core\MVC\Symfony\View
 */
interface ContentTypeView
{
    /**
     * Returns the contained ContentType id.
     *
     * @return mixed
     */
    public function getContentTypeId();
}