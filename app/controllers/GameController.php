<?php

class GameController extends AdminBaseController {
	/**
     * 页面：用户中心首页
     * @return Response
     */
    public function getIndex()
    {
        return View::make('game.index');
    }

}