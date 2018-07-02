<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Boards extends CI_Controller {
    public function __construct()
    {
        set_time_limit (0); // Run forever
        ini_set('memory_limit','1024M'); 

        parent::__construct();	
        $this->load->model("board_model");        
        $this->load->helper('url');
    }         
        
    public function view_board_tiles()
    {
        $data = $this->board_model->getTiles();
        $layout_data['content'] = $this->load->view("admin/boards/view_tiles", $data, true);
        $layout_data['page'] = "ViewBoardTiles";
        $this->load->view("layouts/admin", $layout_data);
    }
    
    public function edit_tile($id)
    {
        $data = $this->board_model->getTile($id);
        $layout_data['content'] = $this->load->view("admin/boards/edit_tile", $data, true);
        $layout_data['page'] = "EditBoardTiles";
        $this->load->view("layouts/admin", $layout_data);
    }
    
    public function ajax_update_tile()
    {
        print json_encode(array('success' => $this->board_model->updateTile($_POST)));
        die();
    }
}