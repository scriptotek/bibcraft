<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class NcipSync extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ncip:sync';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Sync loan status from Ncip service.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{

		$user_loans = array();
		$due = array();

        $this->info(sprintf('-[ %s : Synkronisering starter ]------------------------------------', strftime('%Y-%m-%d %H:%M:%S')));

		$this->info('Sjekker om dokumenter har blitt returnert i BIBSYS...');

		$ncip = App::make('ncip.client');

		foreach (Loan::with('document','user','library')->get() as $loan) {
			if ($loan->document->thing_id == 1) {
				$guest_ltid = $loan->library->guest_ltid;
				if (is_null($guest_ltid)) {
					// This would happen only if the guest ltid is removed at some point
					continue;
				}
				$dokid = $loan->document->dokid;
				$ltid = $loan->as_guest ? $guest_ltid : $loan->user->ltid;
				//$loan->as_guest = !$loan->user->in_bibsys;

				if (!isset($user_loans[$ltid])) {
					$response = $ncip->lookupUser($ltid);
					$user_loans[$ltid] = array();
					foreach ($response->loanedItems as $item) {
						$user_loans[$ltid][] = $item['id'];
						$due[$item['id']] = $item['dateDue'];
					}
				}

				if (in_array($dokid, $user_loans[$ltid])) {

					$this->comment($dokid . ' er fortsatt utlånt til ' . $ltid);

					if (is_null($loan->due_at)) {
						Log::info('[Sync] Oppdaterer forfallsdato for [[Document:' . $dokid . ']]');
					}
					$loan->due_at = $due[$dokid];
					$loan->save();
				} else {
					Log::info('[Sync] Dokumentet [[Document:' . $dokid . ']] har blitt returnert i BIBSYS, så vi returnerer det i BIBREX også');

					$this->info($dokid . ' har blitt returnet i BIBSYS');

					$loan->delete(); 	// Kan ha blitt lånt ut til en annen bruker i mellomtiden.
										// Vi bør derfor *ikke* returnere i NCIP
				}

			}
		}

        $this->info(sprintf('-[ %s : Synkronisering ferdig ]------------------------------------', strftime('%Y-%m-%d %H:%M:%S')));

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(

		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(

		);
	}

}
