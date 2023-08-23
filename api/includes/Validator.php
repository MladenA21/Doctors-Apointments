<?php

class Validator {
    private $errors = [];
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function validate($rules) {
        foreach($rules as $dataItem => $rule) {
            $validationsPrimal = explode("|", $rule);
            $validations = [];

            in_array("required", $validationsPrimal) ? $validations['isRequired'] = true : $validations['isRequired'] = false;
            in_array("email", $validationsPrimal)    ? $validations['isEmail']    = true : $validations['isEmail']    = false;
            in_array("phone", $validationsPrimal)    ? $validations['isPhone']    = true : $validations['isPhone']    = false;

            foreach ($validationsPrimal as $item) {
                $data = explode(":", $item);
                if (count($data) == 2) {
                    is_numeric($data[1]) ? $data[1] = $data[1] = (int)$data[1] : "";
                    $validations[$data[0]] = $data[1];
                }
            }

            foreach($validations as $type => $validation) {
                if (is_callable([$this, $type])) {
                    if(!isset($this->data[$dataItem])) {
                        $this->errors[$dataItem] = "Field ". $dataItem . " is missing";
                    }


                    $this->$type($dataItem, $validation);
                }
            }
        }

        if (count($this->errors) > 0) {
            echo Res::json($this->errors, "Validations have failed", 422);
            die;
        }
    }

    private function isRequired($value, $validation) {
        if ($validation && (!isset($this->data[$value]) || $this->data[$value] == "")) {
                $this->errors[$value] = 'Required';
        }
    }

    private function min($value, $validation) {
        if (gettype($this->data[$value]) == "string" && $validation > strlen($this->data[$value])) {
            $this->errors[$value] = 'should be at least ' . $validation . " characters";
        } else if ((gettype($this->data[$value]) == "double" || gettype($this->data[$value]) == "integer") && $validation > $this->data[$value]) {
            $this->errors[$value] = 'should be at least ' . $validation;
        } else if (gettype($this->data[$value]) == "array" &&  $validation > count($this->data[$value])) {
            $this->errors[$value] = 'should have at least ' . $validation . " items";
        }
    }

    private function max($value, $validation) {
        if (gettype($this->data[$value]) == "string" && $validation < strlen($this->data[$value])) {
            $this->errors[$value] = 'shouldn\'t be more than ' . $validation . " characters";
        } else if ((gettype($this->data[$value]) == "double" || gettype($this->data[$value]) == "integer") && $validation < $this->data[$value]) {
            $this->errors[$value] = 'shouldn\'t be more than ' . $validation;
        } else if (gettype($this->data[$value]) == "array" && $validation < count($this->data[$value])) {
            $this->errors[$value] = 'shouldn\'t have more than ' . $validation . " items";
        }
    }

    private function type($value, $validation) {
        if ($validation != gettype($this->data[$value])) {
            $this->errors[$value] = 'should be of type ' . $validation;
        }
    }

    private function isEmail($value, $validation) {
        if ($validation && !filter_var($this->data[$value], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$value] = 'Please enter a valid Email.';
        }
    }

    private function isPhone($value, $validation) {
        if ($validation && !preg_match('/^[0-9\-\(\)\/\+\s]*$/', $this->data[$value])) {
            $this->errors[$value] = 'Please enter a valid phone number.';
        }
    }

}
?>