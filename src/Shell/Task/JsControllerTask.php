<?php
namespace FrontendBridge\Shell\Task;

use Bake\Shell\Task\BakeTask;
use Cake\Console\Shell;
use Cake\Utility\Inflector;

class JsControllerTask extends BakeTask
{

    public $pathFragment = '../webroot/js/app/controllers/';

    public $tasks = [
        'Bake.BakeTemplate'
    ];

    /**
     * Main Action
     *
     * @return void
     */
    public function main()
    {
        if (count($this->args) < 2) {
            return $this->error('Please pass the controller and action name.');
        }
        $controllerName = Inflector::camelize($this->args[0]);
        $actionName = Inflector::camelize($this->args[1]);
        $this->plugin = isset($this->params['plugin']) ? $this->params['plugin'] : null;
        $prefix = $this->_getPrefix();
        if ($prefix) {
            $prefix = str_replace('/', '\\', $prefix);
        }
        $this->BakeTemplate->set('prefix', $prefix);
        $this->BakeTemplate->set('controllerName', $controllerName);
        $this->BakeTemplate->set('actionName', $actionName);
        $content = $this->BakeTemplate->generate('FrontendBridge.webroot/js_controller');

        $this->bake($prefix, $controllerName, $actionName, $content);
    }

    public function getPath()
    {
        $path = APP . $this->pathFragment;
        if (isset($this->plugin)) {
            $path = $this->_pluginPath($this->plugin) . 'src/' . $this->pathFragment;
        }
        $prefix = $this->_getPrefix();
        if ($prefix) {
            $path .= Inflector::underscore($prefix) . DS;
        }
        return str_replace('/', DS, $path);
    }

    /**
     * Bakes the JS file
     *
     * @param string $controllerName Controller Name
     * @param string $actionName Action Name
     * @param string $content File Content
     * @return string
     */
    public function bake($prefix, $controllerName, $actionName, $content = '')
    {
        if ($content === true) {
            $content = $this->getContent($action);
        }
        if (empty($content)) {
            return false;
        }
        $this->out("\n" . sprintf('Baking `%s%s/%s` JS controller file...', ($this->plugin ? $this->plugin . '.' : ''), $controllerName, $actionName), 1, Shell::QUIET);
        $path = $this->getPath();
        $filename = $path . Inflector::underscore($controllerName) . '/' . Inflector::underscore($actionName) . '_controller.js';
        $this->createFile($filename, $content);
        return $content;
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->description(
            'Bake a JS Controller for use in FrontendBridge '
        )->addArgument('controller', [
            'help' => 'Controller Name, e.g. Posts',
            'required' => true
        ])->addArgument('action', [
            'help' => 'Action Name, e.g. addPost',
            'required' => true
        ])->addOption('prefix', [
            'help' => 'The namespace/routing prefix to use.'
        ]);
        return $parser;
    }
}
