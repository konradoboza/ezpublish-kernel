<?php

/**
 * File contains: eZ\Publish\SPI\Tests\FieldType\AuthorIntegrationTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI\Tests\FieldType;

use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\AuthorConverter;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\Repository\Values\User\UserReference;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;

/**
 * Integration test for legacy storage field types.
 *
 * This abstract base test case is supposed to be the base for field type
 * integration tests. It basically calls all involved methods in the field type
 * ``Converter`` and ``Storage`` implementations. Fo get it working implement
 * the abstract methods in a sensible way.
 *
 * The following actions are performed by this test using the custom field
 * type:
 *
 * - Create a new content type with the given field type
 * - Load create content type
 * - Create content object of new content type
 * - Load created content
 * - Copy created content
 * - Remove copied content
 *
 * @group integration
 */
class AuthorIntegrationTest extends BaseIntegrationTest
{
    const ADMINISTRATOR_USER_ID = 14;
    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezauthor';
    }

    /**
     * Get handler with required custom field types registered.
     *
     * @return \eZ\Publish\SPI\Persistence\Handler
     */
    public function getCustomHandler()
    {
        $authorConverter = new AuthorConverter();
        $authorConverter->setRepository($this->getRepositoryMock());

        $fieldType = new FieldType\Author\Type();
        $fieldType->setTransformationProcessor($this->getTransformationProcessor());

        return $this->getHandler(
            'ezauthor',
            $fieldType,
            $authorConverter,
            new FieldType\NullStorage()
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function getRepositoryMock()
    {
        $repositoryMock = $this->createMock(Repository::class);
        $repositoryMock
            ->expects($this->any())
            ->method('getPermissionResolver')
            ->willReturn($this->getPermissionResolverMock());
        $repositoryMock
            ->expects($this->any())
            ->method('getUserService')
            ->willReturn($this->getUserServiceMock());
        return $repositoryMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function getPermissionResolverMock()
    {
        $permissionResolverMock = $this->createMock(PermissionResolver::class);
        $permissionResolverMock
            ->expects($this->any())
            ->method('getCurrentUserReference')
            ->willReturn(new UserReference(self::ADMINISTRATOR_USER_ID));
        return $permissionResolverMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function getUserServiceMock()
    {
        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock
            ->expects($this->any())
            ->method('loadUser')
            ->with(self::ADMINISTRATOR_USER_ID)
            ->willReturn($this->getContentMock());
        return $userServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function getContentMock()
    {
        $contentMock = $this->createMock(APIContent::class);
        $contentMock
            ->expects($this->any())
            ->method('getName')
            ->willReturn('Administrator User');
        $contentMock
            ->method('__get')
            ->with($this->equalTo('email'))
            ->willReturn('nospam@ez.no');
        return $contentMock;
    }

    /**
     * Returns the FieldTypeConstraints to be used to create a field definition
     * of the FieldType under test.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints
     */
    public function getTypeConstraints()
    {
        return new Content\FieldTypeConstraints();
    }

    /**
     * Get field definition data values.
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function getFieldDefinitionData()
    {
        return [
            // The ezauthor field type does not have any special field definition
            // properties
            ['fieldType', 'ezauthor'],
            [
                'fieldTypeConstraints',
                new Content\FieldTypeConstraints(
                    [
                        'fieldSettings' => new FieldType\FieldSettings(
                            [
                                'defaultAuthor' => null,
                            ]
                        ),
                    ]
                ),
            ],
        ];
    }

    /**
     * Get initial field value.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getInitialValue()
    {
        return new Content\FieldValue(
            [
                'data' => [
                    [
                        'id' => 14,
                        'name' => 'Hans Mueller',
                        'email' => 'hans@example.com',
                    ],
                ],
                'externalData' => null,
                'sortKey' => null,
            ]
        );
    }

    /**
     * Get update field value.
     *
     * Use to update the field
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getUpdatedValue()
    {
        return new Content\FieldValue(
            [
                'data' => [
                    [
                        'id' => 14,
                        'name' => 'Hans Mueller',
                        'email' => 'hans@example.com',
                    ],
                    [
                        'id' => 10,
                        'name' => 'Lieschen Mueller',
                        'email' => 'lieschen@example.com',
                    ],
                ],
                'externalData' => null,
                'sortKey' => null,
            ]
        );
    }
}
