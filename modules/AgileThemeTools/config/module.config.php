<?php
  namespace AgileThemeTools;

  return array(
      'view_manager' => [
          'template_path_stack' => [
              dirname(__DIR__) . '/view',
          ],
      ],
      'block_layouts' => [
          'factories' => [
              'excerpt' => Service\BlockLayout\ExcerptFactory::class,
              'regionalHtml' => Service\BlockLayout\RegionalHtmlFactory::class,
              'byline' => Service\BlockLayout\BylineFactory::class,
              'representativeImage' => Service\BlockLayout\RepresentativeImageFactory::class,
              'slideshow' => Service\BlockLayout\SlideshowFactory::class,
              'profilePicture' => Service\BlockLayout\ProfilePictureFactory::class,
              'profileBiography' => Service\BlockLayout\ProfileBiographyFactory::class,
              'sectionPageListing' => Service\BlockLayout\SectionPageListingFactory::class,
              'homepageSplash' => Service\BlockLayout\HomepageSplashFactory::class,
              'sectionIntroSplash' => Service\BlockLayout\SectionIntroSplashFactory::class,
          ]
      ],
      'form_elements' => [
          'invokables' => [
              'regionmenu' => Form\Element\RegionMenuSelect::class,
              'SectionListingTemplateSelect' => Form\Element\SectionListingTemplateSelect::class,
          ],
          'factories' => [
              'SectionsMenuSelect' => Service\Form\Element\SectionsMenuSelectFactory::class
          ]
      ],
      'service_manager' => [
              'factories' => [
                  'SectionManager' => Service\Controller\SectionManagerFactory::class,
              ],
          ]
 /*         'router' => [
              'routes' => [
                  'add-page-action' => [
                      'type' => \Zend\Router\Http\Segment::class,
                      'options' => [
                          'route' => '/admin/site/s/:site-slug/add-page',
                          'constraints' => [
                              'site-slug' => '[a-zA-Z0-9_-]+',
                          ],
                          'defaults' => [
                              '__NAMESPACE__' => 'RegionalOptionsForm\Controller',
                              'controller' => 'BlockOptionsAdminController',
                              'action' => 'add',
                          ]
                      ]
                  ]
              ]
          ] */

  );