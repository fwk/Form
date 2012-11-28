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

abstract class AbstractElement implements Element
{
    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $hint;

    /**
     * @var array
     */
    protected $sanitizers   = array();

    /**
     * @var array
     */
    protected $filters      = array();

    /**
     * @var string
     */
    protected $error;

    /**
     * @var Form
     */
    protected $parent;

    /**
     *
     * @param string $name
     * @param string $id
     * @param mixed  $default
     *
     * @return void
     */
    public function __construct($name, $id = null, $default = null)
    {
        $this->setAttr(Element::ATTR_NAME, $name);
        if (!is_null($id)) {
            $this->setAttr(Element::ATTR_ID, $id);
        } else {
            $this->setAttr(Element::ATTR_ID, 'elem'. rand(99,999));
        }

        $this->default = $default;
    }

    /**
     *
     * @param Form $form
     *
     * @return Element
     */
    public function setParent(Form $form)
    {
        $this->parent = $form;

        return $this;
    }

    /**
     *
     * @param string $label
     *
     * @return Element
     */
    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function hasLabel()
     {
        return (isset($this->label) && !empty($this->label));
    }

    /**
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *
     * @param string $hint
     *
     * @return Element
     */
    public function hint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function hasHint()
     {
        return (isset($this->hint) && !empty($this->hint));
    }

    /**
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     *
     * @return Form
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *
     * @return boolean
     */
    public function hasValue()
    {
        return isset($this->attributes[Element::ATTR_VALUE]);
    }

    /**
     *
     * @return boolean
     */
    public function hasDefault()
    {
        return isset($this->default);
    }

    /**
     *
     * @return boolean
     */
    public function hasError()
    {
        return (isset($this->error) && !empty($this->error));
    }

    /**
     *
     * @return Element
     */
    public function clear()
    {
        if (!empty($this->default)) {
            $this->attributes[Element::ATTR_VALUE] = $this->default;
        } else {
            unset($this->attributes[Element::ATTR_VALUE]);
        }

        return $this;
    }

    /**
     *
     * @param string $attrName
     * @param string $attrValue
     *
     * @return Element
     */
    public function setAttr($attrName, $attrValue)
    {
        $this->attributes[$attrName] = $attrValue;

        return $this;
    }

    /**
     *
     * @param string $attrName
     *
     * @return Element
     */
    public function removeAttr($attrName)
    {
        if (\array_key_exists($attrName, $this->attributes)) {
            unset($this->attributes[$attrName]);
        }

        return $this;
    }

    /**
     *
     * @param string $attribute
     *
     * @return string
     */
    public function attribute($attribute, $default = null)
    {
        if (\array_key_exists($attribute, $this->attributes)) {
            return $this->attributes[$attribute];
        }

        return $default;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->attribute(Element::ATTR_NAME);
    }

    /**
     *
     * @return Element
     */
    public function setValue($value)
    {
         foreach ($this->sanitizers as $sanitizer) {
            $value  = $sanitizer->sanitize($value);
         }

         $this->setAttr(Element::ATTR_VALUE, $value);

         return $this;
    }

    /**
     *
     * @return boolean
     */
    public function validate()
    {
        foreach ($this->filters as $valid) {
            $filter  = $valid['filter'];
            $err     = $valid['error'];
            if (!$filter->validate($this->valueOrDefault())) {
                 $this->error = (empty($err) ? true : $err);
                 return false;
            }
        }

        return true;
    }

    /**
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *
     * @return string
     */
    public function value()
    {
        return $this->attribute(Element::ATTR_VALUE);
    }

    /**
     *
     * @return mixed
     */
    public function valueOrDefault()
    {
        if (!$this->hasValue())
        {
            return $this->default;
        }

        return $this->value();
    }

    /**
     *
     * @param string $defaultValue
     *
     * @return Element
     */
    public function setDefault($defaultValue)
    {
        $this->default = $defaultValue;

        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     *
     * @param Filter $filter
     *
     * @return Element
     */
    public function filter(Filter $filter, $errorMessage = null)
    {
        $this->filters[] = array(
            'filter'    => $filter,
            'error'     => $errorMessage
        );

        return $this;
    }

    /**
     *
     * @param Sanitizer $sanitizer
     *
     * @return Element
     */
    public function sanitizer(Sanitizer $sanitizer)
    {
        $this->sanitizers[] = $sanitizer;

        return $this;
    }
}