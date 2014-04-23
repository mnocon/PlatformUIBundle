<?php
/**
 * File containing the SectionHelper class.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\PlatformUIBundle\Helper;

use EzSystems\PlatformUIBundle\Helper\SectionHelperInterface;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute as AuthorizationAttribute;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SectionHelper implements SectionHelperInterface
{
    /**
     * @var eZ\Publish\API\Repository\SectionService
     */
    protected $sectionService;

    /**
     * @var Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    public function __construct(
        SectionService $sectionService, SecurityContextInterface $securityContext
    )
    {
        $this->sectionService = $sectionService;
        $this->securityContext = $securityContext;
    }

    /**
     * @inherited
     */
    public function getSectionList()
    {
        $sections = $this->sectionService->loadSections();
        $list = array();
        foreach ( $sections as $section )
        {
            $list[] = array(
                'section' => $section,
                'contentCount' => $this->sectionService->countAssignedContents( $section ),
                'canEdit' => $this->canUser( 'edit' ),
                'canDelete' => $this->canUser( 'edit' ),
                'canAssign' => $this->canUser( 'assign' ),
            );
        }
        return $list;
    }

    /**
     * @inherited
     */
    public function canCreate()
    {
        return $this->canUser( 'edit' );
    }

    /**
     * Checks whether the current user has access to the given $function in the
     * section module.
     *
     * @param string $function
     * @return boolean
     */
    protected function canUser( $function )
    {
        return $this->securityContext->isGranted(
            new AuthorizationAttribute(
                'section',
                $function
            )
        );
    }
}
