<?php defined('BASEPATH') || exit('No direct script access allowed');

class Menu_generator
{
	protected $ci;
	protected $x; //Db Prefix
	protected $uri; //Current uri string
	protected $user_id;
	protected $is_admin;

	public function __construct()
	{
		$this->ci = &get_instance();

		$this->x = $this->ci->db->dbprefix;
		$this->uri = '/' . $this->ci->uri->uri_string() . '/';

		$this->ci->load->helper('app');
		//$this->ci->load->model('menus/users_model');
		$this->ci->load->library('users/auth');
		$this->ci->load->library('session');
		$this->user_id 	= $this->ci->auth->user_id();
		$this->is_admin = $this->ci->auth->is_admin();
	}

	public function build_menus($type = 1)
	{
		$auth = $this->get_auth_permission($this->user_id);
		if (!$auth) {
			$auth = array(NULL);
		}

		if ($type == 1) {
			$menu = $this->ci->db->select("t1.*")
				->from("{$this->x}menus as t1")
				->join("{$this->x}menus as t2", "t1.id = t2.parent_id", "left")
				//->join("{$this->x}menus as t3","t2.id = t3.parent_id","left")
				->where("t1.parent_id", 0)
				->where("t1.group_menu", $type)
				->where("t1.status", 1)
				->group_by("t1.id")
				->order_by("t1.order", "ASC")
				->get()
				->result();

			$html = "<ul class='sidebar-menu'>
						<li class='" . check_class('dashboard', TRUE) . "'>
							<a href='" . site_url() . "'>
								<i class='fa fa-dashboard'></i> <span>Create</span>
							</a>
						</li>";

			if (is_array($menu) && count($menu)) {
				foreach ($menu as $rw) {
					$id 		= $rw->id;
					$title 		= $rw->title;
					$link 		= $rw->link;
					$icon 		= $rw->icon;
					$target 	= $rw->target;
					$submenu = $this->ci->db->select("t1.*")
						->from("{$this->x}menus as t1")
						->where("t1.parent_id", $id)
						->where("t1.group_menu", $type)
						->where("t1.status", 1);
					if (!$this->is_admin) {
						$submenu = $submenu->where_in("t1.permission_id", $auth);
					}
					$submenu = $submenu->group_by("t1.id")
						->group_by("t1.id")
						->order_by("t1.order", "ASC")
						->get()
						->result();
					//Jump to end_for point
					if (count($submenu) == 0) {
						if ($link != "#") {
							if (!in_array($rw->permission_id, $auth) && $this->is_admin == FALSE) {
								goto end_for;
							}
							$active = "";
							if (strpos($this->uri, '/' . $link . '/') !== FALSE) {
								$active = "active";
							}
							$html .= "<li class='{$active}'><a href='" . ($link == '#' ? '#' : site_url($link)) . "' " . ($target == '_blank' ? "target='_blank'" : "") . ">
							<i class='{$icon}'></i> &nbsp;&nbsp;&nbsp;<span>" . ucwords($title) . "
							</span></a></li>";
						}
						goto end_for;
					}
					$active = "";
					foreach ($submenu as $sub) {
						if (strpos($this->uri, '/' . $sub->link . '/') !== FALSE) {
							$active = "active";
							break;
						}
					}
					$html .= "
            			  <li class='treeview {$active}'>
                      <a href='#'>
                        <i class='" . $icon . "'></i>
                        <span>" . ucwords($title) . "</span>
                        <span class='pull-right-container'>
						            	<i class='fa fa-angle-left pull-right'></i>
						          	</span>
                      </a>
                      <ul class='treeview-menu'>";

					//Make Sub Menu
					foreach ($submenu as $sub) {
						$subid 		= $sub->id;
						$subtitle 	= $sub->title;
						$sublink 	= $sub->link;
						$subicon 	= $sub->icon;
						$subtarget 	= $sub->target;
						$subtarget = "";
						if ($subtarget == '_blank') {
							$subtarget = "target='_blank'";
						}


						//Check current link
						if (strpos($this->uri, '/' . $sublink . '/') !== FALSE) {
							$active = "active";
						} else {
							$active = "";
						}
						$html .= "
						<li class='" . $active . "'>
							<a href='" . ($sublink == '#' ? '#' : site_url($sublink)) . "'" . " " . $subtarget . ">
							<i class='" . $subicon . "'></i>&nbsp;&nbsp;&nbsp;" . ucwords($subtitle) . "
							</a>
						</li>";
					}
					$html .= "
						</ul>
					</li>";

					//Jump Point
					end_for:;
					//END FOREACH MENU
				}
				$html .= "
					</ul>";
			}
		} else {
			//other menu
		}

		return $html;
	}

	public function show_menus($type = 1)
	{
		$html = '';
		$auth = $this->get_auth_permission($this->user_id);
		if (!$auth) {
			$auth = array(NULL);
		}

		if ($type == 1) {
			$menu = $this->ci->db->select("t1.*")
				->from("{$this->x}menus as t1")
				->join("{$this->x}menus as t2", "t1.id = t2.parent_id", "left")
				//->join("{$this->x}menus as t3","t2.id = t3.parent_id","left")
				->where("t1.parent_id", 0)
				->where("t1.group_menu", $type)
				->where("t1.status", 1)
				->group_by("t1.id")
				->order_by("t1.order", "ASC")
				->get()
				->result();
			$active_dash = (check_class('dashboard', TRUE)) ? 'menu-item-active' : '';
			// <li class='menu-item " . $active_dash . "' aria-haspopup='true'>
			// 	<a href='" . site_url('dashboard/create_documents') . "' class='menu-link'>
			// 	<span class='menu-text'>
			// 	<i class='fa fa-file-alt mr-2 text-primary'></i>
			// 		<h6 class='m-0'>Create Document</h6>
			// 	</span>
			// 	</a>
			// </li>
			$html = "<ul class='menu-nav'>";

			if (is_array($menu) && count($menu)) {
				foreach ($menu as $rw) {
					$id 		= $rw->id;
					$title 		= $rw->title;
					$link 		= $rw->link;
					$icon 		= $rw->icon;
					$target 	= $rw->target;
					$submenu = $this->ci->db->select("t1.*")
						->from("{$this->x}menus as t1")
						->where("t1.parent_id", $id)
						->where("t1.group_menu", $type)
						->where("t1.status", 1);
					if (!$this->is_admin) {
						$submenu = $submenu->where_in("t1.permission_id", $auth);
					}
					$submenu = $submenu->group_by("t1.id")
						->group_by("t1.id")
						->order_by("t1.order", "ASC")
						->get()
						->result();
					//Jump to end_for point
					if (count($submenu) == 0) {
						if ($link != "#") {
							if (!in_array($rw->permission_id, $auth) && $this->is_admin == FALSE) {
								goto end_for;
							}
							$active = "";
							if (strpos($this->uri, '/' . $link . '/') !== FALSE) {
								$active = "menu-item-active";
							}
							$html .= "
							<li class='menu-item {$active}' aria-haspopup='true'>
							<a href='" . ($link == '#' ? '#' : site_url($link)) . "' " . ($target == '_blank' ? "target='_blank'" : "") . "' class='menu-link'>
							<i class='$icon text-info mr-3 my-auto'></i> 
								<span class='menu-text'>
									<h4 class='m-0'>
									" . ucwords($title) . "
									</h4>
								</span>
							</a>
						</li>";
						}
						goto end_for;
					}
					$active = "";
					foreach ($submenu as $sub) {
						if (strpos($this->uri, '/' . $sub->link . '/') !== FALSE) {
							$active = "menu-item-active";
							break;
						}
					}
					$html .= "
            			<li class='menu-item menu-item-submenu " . $active . "' aria-haspopup='true' data-menu-toggle='hover'>
						  	<a href='#' class='menu-link menu-toggle'>
								<i class='$icon text-info mr-3 my-auto'></i>
                        		<span class='menu-text'><h4 class='m-0'>" . ucwords($title) . "</h4></span>
								<i class='menu-arrow h6 my-auto'></i>
                			</a>
							<div class='menu-submenu'>
								<i class='menu-arrow'></i>
								<ul class='menu-subnav'>";
					//Make Sub Menu
					foreach ($submenu as $sub) {
						$subid 		= $sub->id;
						$subtitle 	= $sub->title;
						$sublink 	= $sub->link;
						$subicon 	= $sub->icon;
						$subtarget 	= $sub->target;
						$subtarget = "";
						if ($subtarget == '_blank') {
							$subtarget = "target='_blank'";
						}


						//Check current link
						if (strpos($this->uri, '/' . $sublink . '/') !== FALSE) {
							$active = "menu-item-active";
						} else {
							$active = "";
						}
						$html .= "
						<li class='menu-item " . $active . "' aria-haspopup='true'>
							<a href='" . ($sublink == '#' ? '#' : site_url($sublink)) . "'" . " " . $subtarget . " class='menu-link text-dark-75'>
								<i class='my-auto mr-3 fa fa-angle-right'></i>
								 <h5 class='my-auto'>" . ucwords($subtitle) . "</h5>
							</a>
						</li>";
					}
					$html .= "
						</ul>
					</li>";

					//Jump Point
					end_for:;
					//END FOREACH MENU
				}
				$html .= "
					</ul>";
			}
		} else {
			//other menu
		}

		return $html;
	}

	protected function get_auth_permission($user_id = 0)
	{
		$role_permissions = $this->ci->users_model->select("permissions.id_permission")
			->join("user_groups", "users.id_user = user_groups.id_user")
			->join("group_permissions", "user_groups.id_group = group_permissions.id_group")
			->join("permissions", "group_permissions.id_permission = permissions.id_permission")
			->where("users.id_user", $user_id)
			->find_all();

		$user_permissions = $this->ci->users_model->select("permissions.id_permission")
			->join("user_permissions", "users.id_user = user_permissions.id_user")
			->join("permissions", "user_permissions.id_permission = permissions.id_permission")
			->where("users.id_user", $user_id)
			->find_all();

		$merge = array();
		if ($role_permissions) {
			foreach ($role_permissions as $key => $rp) {
				if (!in_array($rp->id_permission, $merge)) {
					$merge[] = $rp->id_permission;
				}
			}
		}

		if ($user_permissions) {
			foreach ($user_permissions as $key => $up) {
				if (!in_array($up->id_permission, $merge)) {
					$merge[] = $up->id_permission;
				}
			}
		}
		return $merge;
	}

	function group_menus_access()
	{
		$CI 			= &get_instance();
		$data_session	= $CI->session->userdata;
		$company    	= $this->ci->session->company->id_perusahaan;
		$groupID		= $data_session['User']['group_id'];


		$ArrMenu	= array();
		if ($groupID == '1') {
			$Query	= "SELECT * FROM menus WHERE flag_active='1' ORDER BY parent_id,weight,id ASC";
		} else {
			$Query	= "SELECT menus.* FROM menus INNER JOIN group_menus ON menus.id=group_menus.menu_id WHERE menus.flag_active='1' AND group_menus.group_id='$groupID' AND group_menus.company_id='$company' ORDER BY menus.parent_id,menus.weight,menus.id ASC";
		}

		$jumlah		= $CI->db->query($Query)->num_rows();

		if ($jumlah > 0) {
			$hasil		= $CI->db->query($Query)->result_array();
			foreach ($hasil as $key => $val) {
				$ArrMenu[$key]['Menu']['id']		= $val['id'];
				$ArrMenu[$key]['Menu']['name']		= $val['name'];
				$ArrMenu[$key]['Menu']['path']		= $val['path'];
				$ArrMenu[$key]['Menu']['parent_id']	= $val['parent_id'];
				$ArrMenu[$key]['Menu']['weight']	= $val['weight'];
				$ArrMenu[$key]['Menu']['icon']		= $val['icon'];
			}
		}

		$Menus	= $this->rebuild_structure($ArrMenu);
		return $Menus;
	}

	function rebuild_structure($data)
	{
		$childs = array();

		foreach ($data as &$item) {
			$childs[$item['Menu']['parent_id']][] = &$item['Menu'];
			unset($item);
		}

		foreach ($data as &$item) {
			if (isset($childs[$item['Menu']['id']])) {
				$item['Menu']['child'] = $childs[$item['Menu']['id']];
				unset($childs[$item['Menu']['id']]);
			}
		}

		//	pr($childs);exit;
		//	menu that has no parent, append it as parent
		if (count($childs) > 0) {
			foreach ($childs as $key => $child) {
				if ($key != 0) {
					$childs[0][] = $child[0];
					unset($childs[$key]);
				}
			}
		}

		return isset($childs[0]) ? $childs[0] : array();
	}


	// NEW PERMISSION MENU

	public function show_menus_new($type = 1)
	{
		$html = '';
		$group 		= $this->ci->session->group;
		$company    = $this->ci->session->company->id_perusahaan;
		$ArrMenu	= array();
		// if ($group->role == '1') {
		// 	// $Query	= "SELECT * FROM menus WHERE `status`='1' ORDER BY parent_id,id ASC";
		// } else {
		// 	$Query    = "SELECT * FROM view_group_menus WHERE `status`='1' AND `read`='1' ORDER BY parent_id,id ASC";
		$Query	= "SELECT menus.* FROM menus LEFT JOIN group_menus ON menus.id=group_menus.menu_id WHERE menus.status='1' AND group_menus.group_id='$group->id_group' AND group_menus.company_id='$company' AND group_menus.read='1' ORDER BY menus.parent_id,menus.id ASC";
		// }

		$count		= $this->ci->db->query($Query)->num_rows();

		if ($count > 0) {
			$results		= $this->ci->db->query($Query)->result_array();

			foreach ($results as $key => $val) {
				$ArrMenu[$key]['Menu']['id']		= $val['id'];
				$ArrMenu[$key]['Menu']['title']		= $val['title'];
				$ArrMenu[$key]['Menu']['link']		= $val['link'];
				$ArrMenu[$key]['Menu']['parent_id']	= $val['parent_id'];
				$ArrMenu[$key]['Menu']['icon']		= $val['icon'];
				$ArrMenu[$key]['Menu']['flag_new']	= $val['flag_new'];
			}
			$childs = array();

			foreach ($ArrMenu as &$item) {
				$childs[$item['Menu']['parent_id']][] = &$item['Menu'];
				unset($item);
			}
			
			foreach ($ArrMenu as &$item) {
				if (isset($childs[$item['Menu']['id']])) {
					$item['Menu']['child'] = $childs[$item['Menu']['id']];
					unset($childs[$item['Menu']['id']]);
				}
			}
			
			//	pr($childs);exit;
			//	menu that has no parent, append it as parent
			if (count($childs) > 0) {
				foreach ($childs as $key => $child) {
					if ($key != 0) {
						$childs[0][] = $child[0];
						unset($childs[$key]);
					}
				}
			}

			$Menus = isset($childs[0]) ? $childs[0] : array();
			$this->render_left_menus($Menus);
			// return $Menus;
		}
	}

	public function render_left_menus($Menus, $dept = 0)
	{
		//if first render echo wrapper
		if ($dept == 0) {
			echo '<ul class="menu-nav">';
			// echo '<li class="header">MAIN NAVIGATION</li>';
		}

		//loop children
		foreach ($Menus as $key => $value) :
			$path 	= $value['link'] == '' ? 'javascript:void()' : base_url() .  strtolower($value['link']);
			$icons 	= $value['icon'];
			$new 	= ($value['flag_new'] == 0) ? 'd-none' : '';
			if (array_key_exists('child', $value)) :
				echo "<li class='menu-item menu-item-submenu'>
						<a href='" . $path . "' class='menu-link menu-toggle'>
							<i class='$icons text-info mr-3 my-auto'></i>
							<span class='menu-text'><h6 class='m-0 pr-1'>" . ucwords($value['title']) . "</h6> <label class='label label-danger label-inline $new'>New</label></span>
							<i class='menu-arrow h6 my-auto'></i>
						</a>";
				echo ("<div class='menu-submenu'>
						<i class='menu-arrow'></i>
						<ul class='menu-subnav'>");
				$this->render_left_menus($value['child'], $dept + 1);
				echo ('</ul></div>');
			else :
				echo "<li class='menu-item'>
					<a href='" . $path . "' class='menu-link'>
						<i class='$icons text-info mr-3 my-auto'></i>
						<span class='menu-text'><h6 class='m-0 pr-1'>" . ucwords($value['title']) . "</h6><label class='label label-danger label-inline $new'>New</label></span>
						
					</a>";
			endif;
			echo ('</li>');
		endforeach;
		if ($dept == 0) echo ('</ul>');
	}



	// GROUP MENUS

	public function group_menus($disabled = null)
	{
		$html = '';
		$group      = $this->ci->session->group->id_group;
		$company    = $this->ci->session->company->id_perusahaan;

		$ArrMenu    = array();
		$Query    = "SELECT * FROM menus WHERE `status`='1' ORDER BY parent_id,id ASC";
		$count        = $this->ci->db->query($Query)->num_rows();
		if ($count > 0) {
			$results        = $this->ci->db->query($Query)->result_array();

			foreach ($results as $key => $val) {
				$ArrMenu[$key]['Menu']['id']          = $val['id'];
				$ArrMenu[$key]['Menu']['title']       = $val['title'];
				$ArrMenu[$key]['Menu']['parent_id']   = $val['parent_id'];
				// $ArrMenu[$key]['Menu']['read']   = $val['read'];
				// $ArrMenu[$key]['Menu']['create']   = $val['create'];
				// $ArrMenu[$key]['Menu']['update']   = $val['update'];
				// $ArrMenu[$key]['Menu'['approve']   = $val['approve'];
				// $ArrMenu[$key]['Menu']]['delete']   = $val['delete'];
				// $ArrMenu[$key]['Menu']['download']   = $val['download'];
			}

			$childs = array();

			foreach ($ArrMenu as &$item) {
				$childs[$item['Menu']['parent_id']][] = &$item['Menu'];
				unset($item);
			}

			foreach ($ArrMenu as &$item) {
				if (isset($childs[$item['Menu']['id']])) {
					$item['Menu']['child'] = $childs[$item['Menu']['id']];
					unset($childs[$item['Menu']['id']]);
				}
			}

			//	pr($childs);exit;
			//	menu that has no parent, append it as parent
			if (count($childs) > 0) {
				foreach ($childs as $key => $child) {
					if ($key != 0) {
						$childs[0][] = $child[0];
						unset($childs[$key]);
					}
				}
			}

			$Menus = isset($childs[0]) ? $childs[0] : array();

			$this->render_menus($Menus, 0, $disabled);
		}
	}

	private function render_menus($Menus, $dept = 0, $disabled = null)
	{
		$id_group =  $this->ci->uri->segment(3);
		$company    = $this->ci->session->company->id_perusahaan;

		$group_menus = $this->ci->db->get_where('view_group_menus', ['group_id' => $id_group, 'company_id' => $company, 'status' => '1'])->result_array();
		foreach ($group_menus as $grp) {
			$ArrMenu[$grp['menu_id']] = $grp;
		}

		//if first render echo wrapper
		if ($dept == 0) {
			echo '<tbody>';
			// echo '<li class="header">MAIN NAVIGATION</li>';
		}

		//loop children
		foreach ($Menus as $key => $value) {
			$in_array = isset($ArrMenu[$value['id']]) ? $ArrMenu[$value['id']] : '';
			$path = $value['title'] == '' ? '#' : base_url() .  strtolower($value['title']);
			echo "<tr class='parent table-light'>
				<th>
				<input type='hidden' name='menus[" . $value['parent_id']  . $key . "][id]' value='" . $value['id'] . "'>" . ucwords($value['title']) . "</th>
				<td class='text-center'>
					<div class='checkbox-inline d-flex justify-content-center m-auto' >
						<label class='checkbox checkbox-light-primary d-flex justify-content-center d-inline-block w-100px'>
							<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input parent parent-read parent-read-" . $value['id'] . "' " . (($in_array && ($in_array['read'] == '1')) ? 'checked' : '') . "  type='checkbox' name='menus[" . $value['parent_id']  . $key . "][read]' data-action='read' data-id='" . $value['id'] . "' value=''>
							<span></span>
						</label>
					</div>
				</td>
				<td class='text-center'>
					<div class='checkbox-inline d-flex justify-content-center m-auto'>
						<label class='checkbox checkbox-light-primary d-flex justify-content-center d-inline-block w-100px'>
							<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input parent parent-create parent-create-" . $value['id'] . "' " . (($in_array && ($in_array['create'] == '1')) ? 'checked' : '') . " type='checkbox' name='menus[" . $value['parent_id']  . $key . "][create]' data-action='create' data-id='" . $value['id'] . "' value=''>
							<span></span>
						</label>
					</div>
				</td>
				<td class='text-center'>
					<div class='checkbox-inline d-flex justify-content-center m-auto'>
						<label class='checkbox checkbox-light-primary d-flex justify-content-center d-inline-block w-100px'>
							<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input parent parent-update parent-update-" . $value['id'] . "' " . (($in_array && ($in_array['update'] == '1')) ? 'checked' : '') . " type='checkbox' name='menus[" . $value['parent_id']  . $key . "][update]' data-action='update' data-id='" . $value['id'] . "' value=''>
							<span></span>
						</label>
					</div>
				</td>
				<td class='text-center'>
					<div class='checkbox-inline d-flex justify-content-center m-auto'>
						<label class='checkbox checkbox-light-primary d-flex justify-content-center d-inline-block w-100px'>
							<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input parent parent-delete parent-delete-" . $value['id'] . "' " . (($in_array && ($in_array['delete'] == '1')) ? 'checked' : '') . " type='checkbox' name='menus[" . $value['parent_id']  . $key . "][delete]' data-action='delete' data-id='" . $value['id'] . "' value=''>
							<span></span>
						</label>
					</div>
				</td>
				<td class='text-center'>
					<div class='checkbox-inline d-flex justify-content-center m-auto'>
						<label class='checkbox checkbox-light-primary d-flex justify-content-center d-inline-block w-100px'>
							<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input parent parent-download parent-download-" . $value['id'] . "' " . (($in_array && ($in_array['download'] == '1')) ? 'checked' : '') . " type='checkbox' name='menus[" . $value['parent_id']  . $key . "][download]' data-action='download' data-id='" . $value['id'] . "' value=''>
							<span></span>
						</label>
					</div>
				</td>";
			// $this->render_menus($value['child'], $dept + 1, $disabled);
			echo ('</tr>');
			if (isset($value['child'])) {
				foreach ($value['child'] as $key => $child) {
					$in_array_child = isset($ArrMenu[$child['id']]) ? $ArrMenu[$child['id']] : '';
					// exit;
					echo "<tr class='children'>
					<td class='pl-5'><i class='fa fa-minus mr-2'></i>
					<input type='hidden' name='submenus[" . $child['parent_id'] .  $key . "][id]' value='" . $child['id'] . "'>" . ucwords($child['title']) . "</td>
					<td class='text-center'>
						<div class='checkbox-inline d-flex justify-content-center m-auto'>
							<label class='checkbox checkbox-outline d-flex justify-content-center d-inline-block w-100px'>
								<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input child child-read child-read-" . $child['parent_id'] . "' " . (($in_array_child && ($in_array_child['read'] == '1')) ? 'checked' : '') . " type='checkbox' name='submenus[" . $child['parent_id'] . $key . "][read]' data-id='" . $child['id'] . "' data-parent='" . $child['parent_id'] . "' data-action='read' value=''>
								<span></span>
							</label>
						</div>
					</td>
					<td class='text-center'>
						<div class='checkbox-inline d-flex justify-content-center m-auto'>
							<label class='checkbox checkbox-outline d-flex justify-content-center d-inline-block w-100px'>
								<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input child child-create child-create-" . $child['parent_id'] . "'" . (($in_array_child && ($in_array_child['create'] == '1')) ? 'checked' : '') . "  type='checkbox' name='submenus[" . $child['parent_id'] . $key . "][create]' data-parent='" . $child['parent_id'] . "' data-id='" . $child['id'] . "' data-action='create' value=''>
								<span></span>
							</label>
						</div>
					</td>
					<td class='text-center'>
						<div class='checkbox-inline d-flex justify-content-center m-auto'>
							<label class='checkbox checkbox-outline d-flex justify-content-center d-inline-block w-100px'>
								<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input child child-update child-update-" . $child['parent_id'] . "' " . (($in_array_child && ($in_array_child['update'] == '1')) ? 'checked' : '') . " type='checkbox' name='submenus[" . $child['parent_id'] . $key . "][update]' data-parent='" . $child['parent_id'] . "' data-id='" . $child['id'] . "' data-action='update' value=''>
								<span></span>
							</label>
						</div>
					</td>
					<td class='text-center'>
						<div class='checkbox-inline d-flex justify-content-center m-auto'>
							<label class='checkbox checkbox-outline d-flex justify-content-center d-inline-block w-100px'>
								<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input child child-delete child-delete-" . $child['parent_id'] . "' " . (($in_array_child && ($in_array_child['delete'] == '1')) ? 'checked' : '') . " type='checkbox' name='submenus[" . $child['parent_id']  . $key . "][delete]' data-parent='" . $child['parent_id'] . "' data-id='" . $child['id'] . "' data-action='delete' value=''>
								<span></span>
							</label>
						</div>
					</td>
					<td class='text-center'>
						<div class='checkbox-inline d-flex justify-content-center m-auto'>
							<label class='checkbox checkbox-outline d-flex justify-content-center d-inline-block w-100px'>
								<input " . (($disabled) ? 'disabled' : '') . " class='form-check-input child child-download child-download-" . $child['parent_id'] . "' " . (($in_array_child && ($in_array_child['download'] == '1')) ? 'checked' : '') . " type='checkbox' name='submenus[" . $child['parent_id']  . $key . "][download]' data-parent='" . $child['parent_id'] . "' data-id='" . $child['id'] . "' data-action='download' value=''>
								<span></span>
							</label>
						</div>
					</td>";
					// }
					echo ('</tr>');
				}
			}
		}
		if ($dept == 0) echo ('</tbody>');
	}
}
