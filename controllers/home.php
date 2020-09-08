<?php
class HomeController extends Controller{
    protected function getName() {
        return 'home';
    }

    protected function Index(){
        $model = new Home();
        $this->returnView('index');
    }
}
