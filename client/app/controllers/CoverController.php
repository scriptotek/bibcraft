<?php

class CoverController extends BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    public function getIsbn($isbn) {

        if (Request::ajax()) {
            
        }

        $url = 'http://innhold.bibsys.no/bilde/forside/?size=stor&id=' . $isbn . '.jpg';
        try {
            $s = @file_get_contents($url);
        } catch (ErrorException $e) {
            return json_encode(array(
                'url' => $url,
                'exists' => false
            ));            
        }

        return Response::JSON(array(
            'url' => $url,
            'exists' => ($s != "")
        ));

    }



}