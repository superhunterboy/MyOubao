<?php

class ReportController extends UserBaseController {
    protected $resourceView = 'centerUser.report';

    // public function index()
    // {
    //     return View::make($this->resourceView . '.records-game');
    // }
    // public function create()
    // {
    //     return View::make('l.index');
    // }
    // public function destroy($id)
    // {
    //     return View::make('l.index');
    // }
    // public function edit()
    // {
    //     return View::make('l.index');
    // }
    // public function view()
    // {
    //     return View::make($this->resourceView . '.records-game-detail');
    // }
    public function agentDividend()
    {
        return $this->render();
    }

    public function agentLoss()
    {
        return $this->render();
    }

    public function agentRebate()
    {
        return $this->render();
    }
}