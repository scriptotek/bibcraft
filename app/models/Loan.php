<?php

class Loan extends Eloquent {
	protected $guarded = array();
	protected $softDelete = true;

	public static $rules = array();

	public function document()
    {
        return $this->belongsTo('Document');
    }

	public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array())
    {
        if (Loan::where('document_id','=',$this->document_id)->first()) {
            // already on loan
            return false;
        }
        parent::save($options);
        return true;
    }

}
