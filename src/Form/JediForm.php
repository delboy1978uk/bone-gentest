<?php

declare(strict_types=1);

namespace Random\Developer\Jedi\Form;

use Del\Form\AbstractForm;
use Del\Form\Field\Select;
use Del\Form\Field\Submit;
use Del\Form\Field\Text;
use Random\Developer\Jedi\Entity\Jedi;

class JediForm extends AbstractForm
{
    public function init(): void
    {
        $name = new Text('name');
        $name->setLabel('Jedi Name');
        $name->setRequired(true);
        $this->addField($name);

        $lightsaberColor = new Select('lightsaberColor');
        $lightsaberColor->setOptions([
             1 => 'Blue',
             2 => 'Green',
        ]);
        $lightsaberColor->setLabel('Lightsaber Color');
        $lightsaberColor->setRequired(true);
        $this->addField($lightsaberColor);

        $submit = new Submit('submit');
        $this->addField($submit);
    }
}
