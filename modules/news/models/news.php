
<?php
class NewsModelsNews extends FSModels {
	function __construct() {
		$limit = 10;
		$page = FSInput::get ( 'page' );
		$this->limit = $limit;
		$this->page = $page;
		$fstable = FSFactory::getClass ( 'fstable' );
		$this->table_name = $fstable->_ ( 'fs_news' );
		$this->table_category = $fstable->_ ( 'fs_news_categories' );
	}
	//		function setQuery()
	//		{
	//			$query = " SELECT id,title,summary,image, categoryid, tag
	//						  FROM fs_contents
	//						  WHERE categoryid = $cid 
	//						  	AND published = 1
	//						ORDER BY  id DESC, ordering DESC
	//						 ";
	//			return $query;
	//		}
	/*
		 * get Category current
		 */
	function get_category_by_id($category_id) {
		if (! $category_id)
			return "";
		$query = " SELECT id,name,name_display,is_comment, alias, display_tags,display_title,display_sharing,display_comment,display_category,display_created_time,display_related,updated_time,display_summary
		FROM " . $this->table_category . "  
		WHERE id = $category_id ";
		global $db;
		$sql = $db->query ( $query );
		$result = $db->getObject ();
		return $result;
	}
	
	/*
		 * get Article
		 */
	function get_news(){
		$preview = FSInput::get ('preview');
		$id = FSInput::get ( 'id', 0, 'int' );
		if ($id) {
			$where = " AND id = '$id' ";
		} else {
			$code = FSInput::get ( 'code' );
			if (! $code)
				die ( 'Not exist this url' );
			$where = " AND alias = '$code' ";
		}
		$fs_table = FSFactory::getClass ( 'fstable' );
		if($preview){
			$query = " SELECT *
			FROM " . $fs_table->getTable ( 'fs_news' ) . " 
			WHERE is_trash = 0 AND category_published = 1
			" . $where . " ";
		}else{
			$query = " SELECT *
			FROM " . $fs_table->getTable ( 'fs_news' ) . " 
			WHERE published = 1 AND is_trash = 0 AND category_published = 1
			" . $where . " ";
		}
		
		global $db;
		$sql = $db->query ( $query );
		$result = $db->getObject ();
		return $result;
	}

				function check_sale_off($product_id){
		$today_time = date('Y-m-d H:i:s');
		$fs_table = FSFactory::getClass ( 'fstable' );
		$query = " SELECT id,name, finished_time
		FROM " . $fs_table->getTable ( 'fs_sales' ) . "
		WHERE published = 1 AND started_time < '".$today_time ."' AND finished_time > '".$today_time."' AND type = 1 ORDER BY ordering ASC";
		global $db;
		$sql = $db->query ( $query );
		$sale = $db->getObject ();
		if($sale) {
			$query2 = " SELECT s.price
			FROM " . $fs_table->getTable ( 'fs_sales_products' ) . " as s INNER JOIN ".$fs_table->getTable ( 'fs_products' ) ." as p ON s.product_id = p.id
			WHERE published = 1 AND sale_id = ".$sale-> id." AND p.id = $product_id
			";
			$sql2 = $db->query ( $query2 );
			$result = $db->getObject ();
			if($result) {
			return $result;	
		} else {
			return 0;
		}
		}
		else {
			return 0;
		}
	}

	function get_product_in ( $arr_ids ){
		if (!$arr_ids)
			return;
		$where = 'AND (';
		$arr_id = explode("_",$arr_ids);
		$ii =0;
		foreach ($arr_id as $ar_id) {
			if(!$ii){
				$where .= ' id = '.$ar_id;
			} else {
				$where .= ' OR id = '.$ar_id;
			}
			$ii++;
		}
		$where .= ')';
		$fs_table = FSFactory::getClass ( 'fstable' );
		$query = " SELECT  name,id , image,price,price_old,discount, alias,category_alias,category_id,quantity, price,price_old,types,manufactory_image,manufactory_name,date_start,gift,name_display,date_end,warranty,is_hotdeal,summary,is_new,style_types,accessories,promotion_info,rate_sum, rate_count,name_core, is_hot
		FROM " . $fs_table->getTable ( 'fs_products' ) . "
		WHERE published = 1
		".$where."
		ORDER BY  ordering DESC , id DESC
		";
		global $db;
		$sql = $db->query ( $query );
		$result = $db->getObjectList ();
		return $result;
	}
	
	function getNewerNewsList($cid, $created_time) {
		global $db;
		$limit = 10;
		$id = FSInput::get ( 'id' );
		$query = " SELECT id,title,created_time, category_id 
		FROM " . $this->table_name . "
		WHERE id <> $id
		AND category_id = $cid
		AND published = 1
		AND ( created_time > '$created_time' OR id > $id)
		ORDER BY  id DESC, ordering DESC
		LIMIT 0,$limit
		";
		$db->query ( $query );
		$result = $db->getObjectList ();
		
		return $result;
	}
	function getOlderNewsList($cid, $created_time) {
		global $db;
		$limit = 10;
		$id = FSInput::get ( 'id' );
		$query = " SELECT id,title ,created_time,category_id
		FROM " . $this->table_name . "
		WHERE id <> $id
		AND category_id = $cid
		AND published = 1
		AND ( created_time < '$created_time' OR id < $id)
		ORDER BY  id DESC, ordering DESC
		LIMIT 0,$limit
		";
		$db->query ( $query );
		$result = $db->getObjectList ();
		
		return $result;
	}
	
	function getRelateNewsList($cid) {
		if (! $cid)
			die ();
		$code = FSInput::get ( 'code' );
		$where = '';
		if ($code) {
			$where .= " AND alias <> '$code' ";
		} else {
			$id = FSInput::get ( 'id', 0, 'int' );
			if (! $id)
				die ( 'Not exist this url' );
			$where .= " AND id <> '$id' ";
		}
		
		global $db;
		$limit = 6;
		$fs_table = FSFactory::getClass ( 'fstable' );
		
		$query = " SELECT id,title,alias,image, category_id,category_alias,updated_time,summary,created_time  
		FROM " . $fs_table->getTable ( 'fs_news' ) . "
		WHERE alias <> '" . $code . "'
		AND category_id = $cid
		AND published = 1
		" . $where . "
		ORDER BY  id DESC, ordering DESC
		LIMIT 0,$limit
		";
		$db->query ( $query );
		$result = $db->getObjectList ();
		
		return $result;
	}
	
	/* 
		 * get array [id] = alias
		 */
	function get_content_category_ids($str_ids) {
		if (! $str_ids)
			return;
		$fs_table = FSFactory::getClass ( 'fstable' );
		
		// search for category
		

		$query = " SELECT id,alias
		FROM " . $fs_table->getTable ( 'fs_news_categories' ) . "
		WHERE id IN (" . $str_ids . ")
		";
		
		global $db;
		$sql = $db->query ( $query );
		$result = $db->getObjectList ();
		$array_alias = array ();
		if ($result)
			foreach ( $result as $item ) {
				$array_alias [$item->id] = $item->alias;
			}
			return $array_alias;
		}

		function get_comments($news_id) {
			global $db;
			if (! $news_id)
				return;

		//			$limit = 5;
		//			$id = FSInput::get('id');
			$query = " SELECT name,created_time,id,email,comment,parent_id,level,news_id
			FROM fs_news_comments
			WHERE news_id = $news_id
			AND published = 1
			ORDER BY  created_time  DESC
			";
			$db->query ( $query );
			$result = $db->getObjectList ();
			$tree = FSFactory::getClass ( 'tree', 'tree/' );
			$list = $tree->indentRows2 ( $result );
			return $list;
		}

		function save_rating() {
			$id = FSInput::get ( 'id', 0, 'int' );
			$rate = FSInput::get ( 'rate', 0, 'int' );

			$sql = " UPDATE  fs_news
			SET rating_count = rating_count + 1,
			rating_sum = rating_sum + " . $rate . "
			WHERE id = " . $id . "
			";
			global $db;
		//$db->query($sql);
			$rows = $db->affected_rows ( $sql );

		// save cookies
			if ($rows) {
				$cookie_rating = isset ( $_COOKIE ['rating_news'] ) ? $_COOKIE ['rating_news'] : '';
				$cookie_rating .= $id . ',';
			setcookie ( "rating_news", $cookie_rating, time () + 60 ); //60s
		}
		return $rows;
	}
	
	function get_comment_by_id($comment_id) {
		if (! $comment_id)
			return false;
		$query = " SELECT * 
		FROM fs_contents_comments
		WHERE id =  $comment_id
		AND published = 1
		";
		global $db;
		$db->query ( $query );
		return $result = $db->getObject ();
	}
	function get_relate_by_tags($tags, $exclude = '', $category_id) {
		if (! $tags)
			return;
		$arr_tags = explode ( ',', $tags );
		$where = ' WHERE 1 = 1';
		$where .= ' AND  category_id_wrapper like "%,' . $category_id . ',%" ';
		
		if ($exclude)
			$where .= ' AND id <> ' . $exclude . ' ';
		$total_tags = count ( $arr_tags );
		if ($total_tags) {
			$where .= ' AND (';
			$j = 0;
			for($i = 0; $i < $total_tags; $i ++) {
				$item = trim ( $arr_tags [$i] );
				if ($item) {
					if ($j > 0)
						$where .= ' OR ';
					$where .= " title like '%" . addslashes ( $item ) . "%' ";
					$j ++;
				}
			}
			$where .= ' )';
		}
		
		global $db;
		$limit = 10;
		$fs_table = FSFactory::getClass ( 'fstable' );
		
		$query = " SELECT id,title,alias, category_id , image, category_alias,summary
		FROM " . $fs_table->getTable ( 'fs_news' ) . "
		" . $where . "
		ORDER BY  id DESC, ordering DESC
		LIMIT 0,$limit
		";
		$db->query ( $query );
		$result = $db->getObjectList ();
		
		return $result;
	}
	function update_hits($news_id) {
		if (USE_MEMCACHE) {
			$fsmemcache = FSFactory::getClass ( 'fsmemcache' );
			$mem_key = 'array_hits';
			
			$data_in_memcache = $fsmemcache->get ( $mem_key );
			if (! isset ( $data_in_memcache ))
				$data_in_memcache = array ();
			if (isset ( $data_in_memcache [$news_id] )) {
				$data_in_memcache [$news_id] ++;
			} else {
				$data_in_memcache [$news_id] = 1;
			}
			$fsmemcache->set ( $mem_key, $data_in_memcache, 10000 );

		} else {
			if (! $news_id)
				return;
			
		// count
			global $db, $econfig;
			$sql = " UPDATE fs_news 
			SET hits = hits + 1 
			WHERE  id = '$news_id' 
			";
			//$db->query($sql);
			$rows = $db->affected_rows ( $sql );
			return $rows;
		}
	}
	
	function get_products_related($products_related) {
		if (! $products_related)
			return;
		$limit = 4;
		$rest_products_related_ = substr($products_related, 1, -1);  // retourne "abcde"
		
		
		$fs_table = FSFactory::getClass ( 'fstable' );
		$query = " SELECT code, category_name,name,id , image,price,price_old,discount, alias,category_alias,category_id,quantity, price,price_old,types,manufactory_image,manufactory_name,date_start,date_end,warranty,is_hotdeal,summary,accessories
		FROM " . $fs_table->getTable ( 'fs_products' ) . "
		WHERE id IN ( $rest_products_related_ )
		AND published = 1
		ORDER BY  ordering DESC , id DESC
		LIMIT $limit
		";
		global $db;
		$sql = $db->query ( $query );
		$result = $db->getObjectList ();
		return $result;
	}
	function get_products_hot ( $str_prds_id ,$limit ){
		if (! $limit)
			return;
		$where = '';
		if($str_prds_id){
			$where .= ' AND id NOT IN ('. $str_prds_id.') ';
		}
		$fs_table = FSFactory::getClass ( 'fstable' );
		$query = " SELECT  name,id , image,price,price_old,discount, alias,category_alias,category_id,quantity, price,price_old,types,manufactory_image,manufactory_name,date_start,date_end,warranty,is_hotdeal,summary,accessories
		FROM " . $fs_table->getTable ( 'fs_products' ) . "
		WHERE published = 1
		ANd is_hot = 1
		".$where."
		ORDER BY  ordering DESC , id DESC
		LIMIT $limit
		";
		global $db;
		$sql = $db->query ( $query );
		$result = $db->getObjectList ();
		return $result;
	}
	function get_products_new ( $limit ){
		if (! $limit)
			return;
		$where = '';
		$fs_table = FSFactory::getClass ( 'fstable' );
		$query = " SELECT  name,id , image,price,price_old,discount, alias,category_alias,category_id,quantity, price,price_old,types,manufactory_image,manufactory_name,date_start,date_end,warranty,is_hotdeal,summary,accessories
		FROM " . $fs_table->getTable ( 'fs_products' ) . "
		WHERE published = 1
		".$where."
		ORDER BY  ordering DESC , id DESC
		LIMIT $limit
		";
		global $db;
		$sql = $db->query ( $query );
		$result = $db->getObjectList ();
		return $result;
	}
	
	function get_products_relate_tags($tags, $exclude = '') {
		if (! $tags)
			return;
		$arr_tags = explode ( ',', $tags );
		$where = ' WHERE 1 = 1';
		if ($exclude)
			$where .= ' AND id <> ' . $exclude . ' ';
		$total_tags = count ( $arr_tags );
		if ($total_tags) {
			$where .= ' AND (';
			$j = 0;
			for($i = 0; $i < $total_tags; $i ++) {
				$item = trim ( $arr_tags [$i] );
				if ($item) {
					if ($j > 0)
						$where .= ' OR ';
					$where .= " name like '%" . addslashes ( $item ) . "%' ";
					$j ++;
				}
			}
			$where .= ' )';
		}
		global $db;
		$limit = 4;
		$fs_table = FSFactory::getClass ( 'fstable' );
		$query = " SELECT name,id , image,price,price_old,discount, alias,category_alias,category_id,quantity, price,price_old,types,manufactory_image,manufactory_name,date_start,date_end,warranty,is_hotdeal,summary,accessories
		FROM " . $fs_table->getTable ( 'fs_products' ) . "
		" . $where . "
		ORDER BY  ordering DESC, id DESC
		LIMIT 0,$limit
		";
		$db->query ( $query );
		$result = $db->getObjectList ();
		
		return $result;
	}

	function get_types(){
		return $list = $this -> get_records('published = 1','fs_products_types','id,name,image,alias','ordering ASC');
	}

	function get_products_tag_group($products_related) {
		if (!$products_related || $products_related =='' || $products_related==',' || $products_related==',,' || $products_related==',,,' )
			return;
		$limit = 15;
		$rest_products_related_ = substr($products_related, 1, -1);  // retourne "abcde"
		
		$fs_table = FSFactory::getClass ( 'fstable' );
		$query = " SELECT  *
		FROM " . $fs_table->getTable ( 'fs_products_tags' ) . "
		WHERE id IN ( $rest_products_related_ )
		AND published = 1
		ORDER BY  ordering ASC , id DESC
		LIMIT $limit
		";
		
		global $db;
		$sql = $db->query ( $query );
		$result = $db->getObjectList ();
		return $result;
	}
}

?>