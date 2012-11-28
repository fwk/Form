<?php
/**
 * Fwk
 *
 * Copyright (c) 2011-2012, Julien Ballestracci <julien@nitronet.org>.
 * All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5.3
 *
 * @category  Forms
 * @package   Fwk\Form
 * @author    Julien Ballestracci <julien@nitronet.org>
 * @copyright 2011-2012 Julien Ballestracci <julien@nitronet.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://www.phpfwk.com
 */
namespace Fwk\Form;

use Fwk\Form\Exceptions\RendererException;

class Renderer
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var array
     */
    protected $resources = array();

    /**
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct(array $options = array())
    {
        $this->options = array_merge(array(
            'resourcesDir'  => __DIR__ .'/Resources'
        ), $options);

        $this->addResources(array(
            'Fwk\Form\Elements\Text'     => 'input-text.phtml',
            'Fwk\Form\Elements\Submit'   => 'input-submit.phtml',
            'Fwk\Form\Elements\Checkbox' => 'input-check.phtml',
            'Fwk\Form\Elements\Password' => 'input-password.phtml',
            'Fwk\Form\Elements\Hidden'   => 'input-hidden.phtml',
            'Fwk\Form\Elements\TextArea' => 'textarea.phtml',
            'Fwk\Form\Elements\Select'   => 'select.phtml',
            'Fwk\Form\Elements\Group'    => 'group.phtml'
        ));
    }

    /**
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = false)
    {
        if (\array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return $default;
    }

    /**
     *
     * @return array
     */
    public function getAll()
    {
        return $this->options;
    }

    /**
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Renderer
     */
    public function set($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Renderer
     */
    public function setAll(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has($key)
    {
        return \array_key_exists($key, $this->options);
    }

    /**
     *
     * @param string $key
     *
     * @return Renderer
     */
    public function remove($key)
    {
        unset($this->options[$key]);

        return $this;
    }

    /**
     *
     * @param string $elementClass
     * @param string $resourceFile
     *
     * @return Renderer
     */
    public function addResource($elementClass, $resourceFile)
    {
        $this->resources[$elementClass] = $resourceFile;

        return $this;
    }

    /**
     *
     * @param array $resources
     *
     * @return Renderer
     */
    public function addResources(array $resources)
    {
        $this->resources = array_merge($this->resources, $resources);

        return $this;
    }

    /**
     *
     * @param string $elementClass
     *
     * @return Renderer
     */
    public function removeResource($elementClass)
    {
        unset($this->resources[$elementClass]);

        return $this;
    }

    /**
     *
     * @param \Fwk\Form\Element $element
     *
     * @return string
     */
    public function getElementResource(Element $element)
    {
        $class = get_class($element);
        if(!isset($this->resources[$class])) {
            throw new RendererException(sprintf('Missing resource for Element of class "%s"', $class));
        }

        $file = $this->get('resourcesDir') . DIRECTORY_SEPARATOR . $this->resources[$class];
        if (!is_file($file)) {
            throw new RendererException(sprintf('Missing resource file "%s"', $this->resources[$class]));
        }
        return $this->get('resourcesDir') . DIRECTORY_SEPARATOR . $this->resources[$class];
    }

    /**
     *
     * @param Form $form
     *
     * @return string
     */
    public function render(Form $form)
    {
        $contents = "";

        $form->notify(new FormEvent(Events::BEFORE_RENDER, $form, array(
            'renderer'  => $this,
            'contents'  => &$contents
        )));

        ob_start();
        $resource = $this->get('resourcesDir') . DIRECTORY_SEPARATOR . 'form.phtml';
        include $resource;
        $contents .= ob_get_clean();

        $form->notify(new FormEvent(Events::AFTER_RENDER, $form, array(
            'renderer'  => $this,
            'contents'  => &$contents
        )));

        return $contents;
    }

    /**
     *
     * @param Form $form
     *
     * @return string
     */
    public function renderElement(Element $element, Form $form)
    {
        $contents = "";

        $form->notify(new FormEvent(Events::BEFORE_ELEMENT_RENDER, $form, array(
            'renderer'  => $this,
            'contents'  => &$contents
        ), $element));

        ob_start();
        $resource = $this->getElementResource($element);
        include $resource;
        $contents .= ob_get_clean();

        $form->notify(new FormEvent(Events::AFTER_ELEMENT_RENDER, $form, array(
            'renderer'  => $this,
            'contents'  => &$contents
        ), $element));

        return $contents;
    }
}