<?php

namespace Geissler\Converter\Standard\MLA;

use Geissler\Converter\Standard\Basic\StandardAbstract;

/**
 * @author Níckolas Da Silva <nickolas@phpsp.org.br>
 * @copyright ResearchGate GmbH
 * @license MIT
 */
class MLA extends StandardAbstract
{
    public function __construct($data = '')
    {
        parent::__construct($data);

        $this->setParser(new Parser());
        $this->setCreator(new Creator());
    }
}
