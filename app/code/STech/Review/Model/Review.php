<?php
namespace STech\Review\Model;

class Review extends \Magento\Review\Model\Review
{
    public function validate()
    {
        $errors = [];

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}