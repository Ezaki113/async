<?php
declare (strict_types = 1);

$refl = new ReflectionClass('UV');
print_r($refl->getConstants());
