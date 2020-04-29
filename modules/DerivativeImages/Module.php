<?php
/*
 * Derivative Images
 *
 * Recreate (or create) derivative images.
 *
 * Copyright Daniel Berthereau, 2018
 *
 * This software is governed by the CeCILL license under French law and abiding
 * by the rules of distribution of free software.  You can use, modify and/ or
 * redistribute the software under the terms of the CeCILL license as circulated
 * by CEA, CNRS and INRIA at the following URL "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy, modify
 * and redistribute granted by the license, users are provided only with a
 * limited warranty and the software's author, the holder of the economic
 * rights, and the successive licensors have only limited liability.
 *
 * In this respect, the user's attention is drawn to the risks associated with
 * loading, using, modifying and/or developing or reproducing the software by
 * the user in light of its specific status of free software, that may mean that
 * it is complicated to manipulate, and that also therefore means that it is
 * reserved for developers and experienced professionals having in-depth
 * computer knowledge. Users are therefore encouraged to load and test the
 * software's suitability as regards their requirements in conditions enabling
 * the security of their systems and/or data to be ensured and, more generally,
 * to use and operate it in the same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 */

namespace DerivativeImages;

use DerivativeImages\Form\ConfigForm;
use DerivativeImages\Job\DerivativeImages;
use Omeka\Module\AbstractModule;
use Omeka\Stdlib\Message;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Renderer\PhpRenderer;

/**
 * Derivative Images
 *
 * Recreate (or create) derivative images.
 *
 * @copyright Daniel Berthereau, 2018
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 */
class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getConfigForm(PhpRenderer $renderer)
    {
        $services = $this->getServiceLocator();
        $form = $services->get('FormElementManager')->get(ConfigForm::class);
        $form->init();
        $html = '<p>'
            . sprintf(
                $renderer->translate('Set your parameters (type, size…) in %sconfig/local.config.php%s, then fill this form to recreate derivative thumbnails.'), // @translate
                '<code>', '</code>'
            )
            . '</p>';
        $html .= $renderer->formCollection($form);
        $html .= '<p>'
            . $renderer->translate('When the process is ended (check the job log), this module can be uninstalled.') // @translate
            . '</p>';
        return $html;
    }

    public function handleConfigForm(AbstractController $controller)
    {
        $services = $this->getServiceLocator();
        $form = $services->get('FormElementManager')->get(ConfigForm::class);

        $params = $controller->getRequest()->getPost();

        $form->init();
        $form->setData($params);
        if (!$form->isValid()) {
            $controller->messenger()->addErrors($form->getMessages());
            return false;
        }

        $params = $form->getData();

        if (empty($params['process']) || $params['process'] !== $controller->translate('Process')) {
            $message = 'No job launched.'; // @translate
            $controller->messenger()->addWarning($message);
            return;
        }

        unset($params['csrf']);
        unset($params['process']);
        $params['ingesters'] = $params['ingesters'] ?: [];
        $params['renderers'] = $params['renderers'] ?: [];
        $params['media_types'] = $params['media_types'] ?: [];

        $dispatcher = $services->get(\Omeka\Job\Dispatcher::class);
        $job = $dispatcher->dispatch(DerivativeImages::class, $params);
        $message = new Message(
            'Creating derivative images in background (%sjob #%d%s)', // @translate
            sprintf('<a href="%s">',
                htmlspecialchars($controller->url()->fromRoute('admin/id', ['controller' => 'job', 'id' => $job->getId()]))
            ),
            $job->getId(),
            '</a>'
        );
        $message->setEscapeHtml(false);
        $controller->messenger()->addSuccess($message);
    }
}
