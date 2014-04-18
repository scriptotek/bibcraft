<?php

class Reminder extends Eloquent {

	public function loans()
    {
        return $this->belongsToMany('Loan');
    }

}
