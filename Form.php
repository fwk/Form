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
 * @category  Form
 * @package   Fwk\Form
 * @author    Julien Ballestracci <julien@nitronet.org>
 * @copyright 2011-2012 Julien Ballestracci <julien@nitronet.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://www.phpfwk.com
 */
namespace Fwk\Form;

use Fwk\Events\Dispatcher,
    Elements\Group;

class Form extends Dispatcher implements \IteratorAggregate
{
    const METHOD_GET        = "get";
    const METHOD_POST       = "post";

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $method       = self::METHOD_POST;

    /**
     * @var boolean
     */
    protected $multipart    = false;

    /**
     * @var array
     */
    protected $elements     = array();

    /**
     * @var array
     */
    protected $options      = array();

    /**
     * @var array
     */
    protected $errors       = array();

    /**
     * @var boolean
     */
    protected $submitted    = false;

    /**
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct($action = null, $method = self::METHOD_POST,
        array $options = array())
    {
        $this->action   = $action;
        $this->method   = $method;
        $this->options  = $options;
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
     * @return Form
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
     * @return Form
     */
    public function setAll(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     *
     * @param string $action
     *
     * @return Form
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isMultipart()
    {
        return $this->multipart;
    }

    /**
     *
     * @param boolean $bool
     *
     * @return Form
     */
    public function setMultipart($bool)
    {
        $this->multipart = $bool;

        return $this;
    }

    /**
     *
     * @param Element $element
     *
     * @return Form
     */
    public function add(Element $element)
    {
        $this->elements[] = $element;
        $element->setParent($this);

        return $this;
    }

    /**
     *
     * @param array $elements
     *
     * @return Form
     */
    public function addAll(array $elements)
    {
        foreach ($elements as $element) {
            $this->add($element);
        }

        return $this;
    }

    /**
     *
     * @param string $elementName
     *
     * @return boolean
     */
    public function has($elementName)
    {
        foreach ($this->elements as $element) {
            if ($element instanceof Group && $element->has($elementName)) {
                return true;
            } elseif ($element->getName() == $elementName) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param string $name
     *
     * @throws Exceptions\UnknownElementException if element is unknown
     * @return Element
     */
    public function element($elementName)
    {
        foreach ($this->elements as $element) {
            if ($element->getName() == $elementName) {
                return $element;
            } elseif ($element instanceof Group) {
                try {
                    return $element->element($elementName);
                } catch(\RuntimeException $e) {
                }
            }
        }

        throw new Exceptions\UnknownElementException(
            sprintf("Unknown Form element '%s'", $elementName)
        );
    }

    /**
     *
     * @param string $elementName
     *
     * @return Form
     */
    public function remove($elementName)
    {
        $elem = $this->element($elementName);
        $final = array();

        foreach ($this->elements as $element) {
            if ($element !== $elem) {
                $final[] = $element;
            }
        }
        $this->elements = $final;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function elements()
    {
        return $this->elements;
    }

    /**
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $it = new \ArrayIterator($this->elements);
        $it->setFlags(\ArrayIterator::ARRAY_AS_PROPS);

        return $it;
    }

    /**
     *
     * @return boolean
     */
    public function isSubmitted()
    {
        return $this->submitted;
    }

    /**
     *
     * @param array $userData
     *
     * @return void
     * @throws Exceptions\AlreadySubmittedException when Form is already submitted
     */
    public function submit(array $userData)
    {
        if ($this->submitted === true) {
            throw new Exceptions\AlreadySubmittedException(
                "Form is already submitted"
            );
        }

        foreach ($this->elements as $element) {
            $name = $element->getName();
            if(!empty($name) && strpos($name, '[]', strlen($name)-2) !== false) {
                $name = substr($name, 0, strlen($name)-2);
            }

            if (isset($userData[$name])) {
                $value = $userData[$name];
            } else {
                $value = null;
            }

            $element->setValue($value);
        }

        $this->submitted = true;
    }

    /**
     *
     * @return boolean
     * @throws Exceptions\NotSubmittedException when form isn't submitted yet
     */
    public function validate()
    {
        if (!$this->isSubmitted()) {
            throw new Exceptions\NotSubmittedException(
                "Form isn't submitted"
            );
        }

        $errors = array();
        foreach ($this->elements as $element) {
            if (!$element->validate()) {
                $message = $element->getError();
                if (!empty($message)) {
                    $name = $element->getName();
                    if(empty($name)) {
                        $name = 'unknown'. rand(0,999);
                    }

                    $errors[$name] = $element->getError();
                }
            }
        }

        $this->errors = $errors;
        if (count($errors)) {
            return false;
        }

        return true;
    }

    /**
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return boolean
     */
    public function hasErrors()
    {
        return ($this->isSubmitted() && count($this->errors) > 0);
    }

    /**
     *
     * @return void
     */
    public function reset()
    {
        $this->submitted = false;
        foreach ($this->elements as $element) {
            $element->clear();
        }
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        $final = array();
        foreach ($this->elements as $element) {
            $name = $element->getName();
            if (empty($name)) {
                continue;
            }

            $final[$name] = $element->valueOrDefault();
        }

        return $final;
    }

    /**
     *
     * @param string $element
     *
     * @return mixed
     */
    public function __get($element)
    {
        return $this->element($element)->valueOrDefault();
    }

    /**
     *
     * @param string $element
     * @param mixed $value
     *
     * @return void
     */
    public function __set($element, $value)
    {
        return $this->element($element)->setValue($value);
    }

    /**
     *
     * @param string $element
     *
     * @return boolean
     */
    public function __isset($element)
    {
        return $this->element($element)->hasValue();
    }

    /**
     *
     * @param string $element
     *
     * @return void
     */
    public function __unset($element)
    {
        $this->element($element)->setValue(null);
    }
}