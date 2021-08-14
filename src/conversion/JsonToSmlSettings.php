<?php

namespace GELight\conversion;

use Exception;

class JsonToSmlSettings {

    public int $case = 0;
    public bool $alreadySet = false;

    public function __construct() {}

    public function scan($name) {
        if ($this->alreadySet) {
            return;
        }
    
        try {
            // HACK
            if (str_starts_with($name, "2")) {
                $this->alreadySet = true;
                $this->case = 0;
                        return;
                    }
            if ($name === strtolower($name)) {
                $this->case = 2;
            } else if ($name[0] === strtolower($name[0])) {
                $this->case = 1;
            }
        } catch (Exception) {
            // ...
        }

        $this->alreadySet = true;
    }

}
