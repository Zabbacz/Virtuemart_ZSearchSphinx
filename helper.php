<?php
defined('_JEXEC') or die('Restricted access');
/**
 * Helper class for SearchSphinx! module
 * 
 * @subpackage Modules
 * @license        GNU/GPL, see LICENSE.php
 * mod_zsearchsphinx is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
class ModZSearchSphinxHelper
{
    public static function getAjax() {
        $input = JFactory::getApplication()->input;
        $q  = $input->get('term', '', 'string');
          $db = JFactory::getDBO();
          $db = JDatabase::getInstance(self::pripojDatabazi('sphinx'));
          $stmt = $db->getQuery(true);
            $aq = explode(' ',$q);
            if(strlen($aq[count($aq)-1])<3){
        	$query = $q;
            }else{
                $query = $q.'*';
            }
            $stmt
                ->select($db->quoteName('product_name'))
                ->from($db->quoteName('#__sphinx_test1'))
                ->where("MATCH"."('".$query."')"." LIMIT  0,10 OPTION ranker=sph04");
        $db->setQuery($stmt);
        $result = $db->query();
        $arr = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $arr[] = array('label' => ($row['product_name']));
        }
    }
    return json_encode($arr);
    }

    public static function getSearch()
    {
    $input = JFactory::getApplication()->input;
    if ($input -> exists('query'))
    {
        $query  = $input->get('query', '', 'string');
        $docs = array();
        $start =0;
        $offset =10;
        $current = 1;
        $url = '';
        $user =& JFactory::getUser();
        $userId = $user->get( 'id' );
        $query = (string) preg_replace('/[^\p{L}\d\s]/u', ' ', $query);
        $query = trim($query);
        $query = $query.'*';
        if ($input -> exists('start'))
        {
        $start  = $input->get('start', '0', 'INT');
	    $current = $start/$offset+1;
	}
        $db = JFactory::getDBO();
        $db = JDatabase::getInstance(self::pripojDatabazi('sphinx'));
        $stmt = $db->getQuery(true);
        $stmt
            ->select ($db->quoteName('id'))
            ->from($db->quoteName('#__sphinx_test1'))
            ->where("MATCH"."('".$query."')"." LIMIT "  . $start .",". $offset." OPTION ranker=sph04,field_weights=(product_name=100)");
        $db->setQuery($stmt);
        $rows = $db->loadAssocList();
        $meta=$db->setQuery('show meta');
        $meta = $db->loadAssocList();
        foreach($meta as $m) {
	    $meta_map[$m['Variable_name']] = $m['Value'];
	}
        $total_found = $meta_map['total_found'];
        $total = $meta_map['total'];
        $total_array = array($total,$total_found,$offset,$current,$start,$query);
 	$ids = array();
        $tmpdocs = array();
//echo $query;
//var_dump($rows);
        if (count($rows)> 0) {
            foreach ($rows as  $v) {
		$ids[] =  $v['id'];
		}
            $db = JFactory::getDBO();
            $db = JDatabase::getInstance(self::pripojDatabazi('joomla'));
        
            $user_group =$db->getQuery(true);
            $user_group
                    ->select ($db->quoteName ('virtuemart_shoppergroup_id'))
                    ->from ($db->quoteName ('#__virtuemart_vmuser_shoppergroups'))
                   ->where ($db->quoteName('virtuemart_user_id'). '=' .$userId);
            $db->setQuery($user_group);
            $row_user_group = $db->loadRow();
            if(!$row_user_group){$row_user_group[0]=5;}
            $q = $db->getQuery(true);
            $q
                ->select ($db->quoteName (array('t1.virtuemart_product_id', 'product_name', 'virtuemart_category_id', 'product_availability','product_price','file_url')))
                ->from($db->quoteName('#__virtuemart_products_cs_cz','t1'))
                ->join('INNER',$db->quoteName('#__virtuemart_product_prices','t4'). ' ON ' . $db->quoteName('t1.virtuemart_product_id') . ' = ' . $db->quoteName('t4.virtuemart_product_id'))    
                ->join('INNER',$db->quoteName('#__virtuemart_products','t3'). ' ON ' . $db->quoteName('t1.virtuemart_product_id') . ' = ' . $db->quoteName('t3.virtuemart_product_id'))
                ->join('INNER',$db->quoteName('#__virtuemart_product_medias','t5'). ' ON ' . $db->quoteName('t1.virtuemart_product_id') . ' = ' . $db->quoteName('t5.virtuemart_product_id'))    
                ->join('INNER',$db->quoteName('#__virtuemart_medias','t6'). ' ON ' . $db->quoteName('t5.virtuemart_media_id') . ' = ' . $db->quoteName('t6.virtuemart_media_id'))
                ->join('LEFT',$db->quoteName('#__virtuemart_product_categories','t2'). ' ON ' . $db->quoteName('t1.virtuemart_product_id') . ' = ' . $db->quoteName('t2.virtuemart_product_id'))
                ->where($db->quoteName('t1.virtuemart_product_id'). ' IN '.'  (' . implode(",", $ids) . ')')
                ->where($db->quoteName('t4.virtuemart_shoppergroup_id'). ' = '.$row_user_group[0]);
            $db->setQuery ($q);
        $q = $db->loadAssocList(); 
//        var_dump($q);            
            foreach ($q as $row) {
                $tmpdocs[$row['virtuemart_product_id']] = array('product_name' => $row['product_name'], 'virtuemart_product_id' => $row['virtuemart_product_id'], 'virtuemart_category_id' => $row['virtuemart_category_id'], 'product_availability' => $row['product_availability'],$row[5] =>['total'], 'product_price' => $row['product_price'],'file_url' => $row['file_url'],$row[8] => ['total_found']);
              //  $tmpdocs[$row['virtuemart_product_id']] = array('product_name' => $row['product_name'], 'virtuemart_product_id' => $row['virtuemart_product_id'], 'virtuemart_category_id' => $row['virtuemart_category_id'], 'product_availability' => $row['product_availability'],$row =>['total'],'product_price' => $row['product_price'],'file_url' => $row['file_url'],$row => ['total_found']);
        } 
            foreach ($ids as $id) {
                $docs[] = $tmpdocs[$id];
    		}
            $last = count ($docs)+1;
            $docs[$last]=$total_array;
	}
    }
    return $docs;  
}
      
private static function pripojDatabazi($database) {
            $option = array(); //prevent problems
            switch ($database)
     {    case 'sphinx':
             $option['driver']   = 'mysqli';            // Database driver name
             $option['host']     = '127.0.0.1:9306';    // Database host name
             $option['user']     = 'xxx';       // User for database authentication
             $option['password'] = 'xxx';   // Password for database authentication
             $option['prefix']   = '';             // Database prefix (may be empty)
             break;
         case 'joomla':
             $option['driver']   = 'mysqli';            // Database driver name
             $option['host']     = 'localhost:3306';    // Database host name
             $option['user']     = 'xxx';       // User for database authentication
             $option['password'] = 'xxx';   // Password for database authentication
             $option['database'] = 'xxx';      // Database name
             $option['prefix']   = 'xxx_';             // Database prefix (may be empty)
             break;

}     
return $option;
       
   }
}
