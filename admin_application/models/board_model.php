<?php 
class XmlElement {
  var $name;
  var $attributes;
  var $content;
  var $children;
};

class board_model extends CI_Model
{        
        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database ('admin', true);
        }
        
        public function getColumnEnum($schema, $table, $column)
        {
            $rs = $this->db->query("Select * from INFORMATION_SCHEMA.COLUMNS where TABLE_SCHEMA = ? and TABLE_NAME = ? and COLUMN_NAME = ?", array($schema, $table, $column));            
            if($rs->num_rows())
            {
                $temp = str_ireplace("enum(", "", trim($rs->row()->COLUMN_TYPE, ")"));
                if(preg_match_all("/'([0-9A-Za-z _]+)'/", $temp, $matches))
                {
                    return $matches[1];
                }                     
            }
            return array();
        }
        
        private function xml_to_object($xml) 
        {
            $parser = xml_parser_create();
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($parser, $xml, $tags);
            xml_parser_free($parser);

            $elements = array();  // the currently filling [child] XmlElement array
            $stack = array();
            foreach ($tags as $tag) {
              $index = count($elements);
              if ($tag['type'] == "complete" || $tag['type'] == "open") {
                $elements[$index] = new XmlElement;
                $elements[$index]->name = $tag['tag'];
                $elements[$index]->attributes = $tag['attributes'];
                $elements[$index]->content = isset($tag['value']) ? $tag['value'] : "";
                if ($tag['type'] == "open") {  // push
                  $elements[$index]->children = array();
                  $stack[count($stack)] = &$elements;
                  $elements = &$elements[$index]->children;
                }
              }
              if ($tag['type'] == "close") {  // pop
                $elements = &$stack[count($stack) - 1];
                unset($stack[count($stack) - 1]);
              }
            }
            return $elements[0];  // the single top-level element
          }
          
          public function parseXml()
          {
              $xml = "";
              $fp = fopen("/Development/citySquaresLargeTileAtlas.xml", "r");
              while($line = fgets($fp))
                      $xml .= $line;
              
              $content = $this->xml_to_object($xml);
              //print_r($content); die();
              foreach($content->children as $element)
              {
                  $attr = $element->attributes;
                  print sprintf("Insert into BoardTiles (name,x,y,width,height) values('%s',%d,%d,%d,%d);\n", $attr['name'], $attr['x'], $attr['y'], $attr['width'], $attr['height']);
              }
          }
          
          public function updateTile($data)
          {
              $this->db->where("id", $data['id']);
              $this->db->update("BoardTiles", $data);
              return true;
          }
          
          public function getTiles()
          {
              $rs = $this->db->query("Select * from BoardTiles");
              $tiles = $rs->result();
              
              $types = array('Events','Tiles');
              $sub_types = array('Multiplier','Spins','Tickets','Letter','City','Sponsor','Unlock','Detour','Anchor','Blank','Warp');
              
              return compact('tiles','types','sub_types');
          }
          
          public function getTile($id)
          {
              $rs = $this->db->query("Select * from BoardTiles where id = ?", array($id));
              $tile = $rs->row();
              $types = $this->getColumnEnum("kizzang", "BoardTiles", "type");
              return compact('tile','types');
          }
          
          public function addTile($data)
          {
              $this->db->where('id', $data['id']);
              $this->db->update('BoardTiles', $data);
              return true;
          }
}
