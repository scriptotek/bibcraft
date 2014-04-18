<?php

use Carbon\Carbon;

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

    public function reminders()
    {
        return $this->belongsToMany('Reminder');
    }

    /**
     * Columns to be converted to instances of Carbon
     */
    public function getDates()
    {
        return array('created_at', 'updated_at', 'deleted_at', 'due_at');
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

    public function daysLeft() {
        return Carbon::now()->diffInDays( $this->due_at, false );
    }

    public function lastReminder()
    {
        $rem = $this->reminders()->orderBy('created_at','desc')->first();
        if ($rem) {
            return $rem->created_at->diffInDays( Carbon::now() );
        } else {
            return null;             
        }
    }

    public function renew()
    {

        $dokid = $this->document->bibsys_dokid;

        // $ncip = App::make('ncip.client');
        // $response = $ncip->renewItem($dokid);

        // if (!$response->success) {
        //     Log::warn('Kunne ikke fornyes i BIBSYS: [[Document:' . $dokid . ']]');
        //     return false;
        // }

        $this->due_at = $this->due_at->addWeeks(4);
        Log::info('Fornyet [[Document:' . $dokid . ']] i BIBSYS');

        $this->save();
        return true;
    }

    /* return */
    public function delete()
    {

        $dokid = $this->document->bibsys_dokid;

        // $ncip = App::make('ncip.client');
        // $response = $ncip->returnItem($dokid);

        // if (!$response->success) {
        //     Log::warn('Kunne ikke returneres i BIBSYS: [[Document:' . $dokid . ']]');
        //     return false;
        // }

        Log::info('Returnerte [[Document:' . $dokid . ']] i BIBSYS');

        parent::delete();
        return true;
    }

}
