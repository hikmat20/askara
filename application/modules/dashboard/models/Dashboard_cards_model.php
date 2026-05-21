<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_cards_model extends BF_Model
{
    protected $table_name = 'dashboard_cards';
    protected $key = 'id';
    protected $created_field = 'created_at';
    protected $modified_field = 'modified_at';
    protected $set_created = false;
    protected $set_modified = false;
    protected $soft_deletes = false;

    public function get_active_cards()
    {
        return $this->db
            ->where('is_active', 'Y')
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->table_name)
            ->result();
    }

    public function get_all_cards()
    {
        return $this->db
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->table_name)
            ->result();
    }

    /**
     * Daftar menu aktif yang punya link valid (bukan #).
     */
    public function get_menu_link_options()
    {
        $menus = $this->db
            ->select('id, title, link, parent_id')
            ->where('status', 1)
            ->where('link !=', '#')
            ->where('link !=', '')
            ->order_by('parent_id', 'ASC')
            ->order_by('order', 'ASC')
            ->order_by('title', 'ASC')
            ->get('menus')
            ->result();

        $parentTitles = [];
        foreach ($menus as $menu) {
            if ((int) $menu->parent_id === 0) {
                $parentTitles[$menu->id] = $menu->title;
            }
        }

        $options = [];
        $links = [];
        foreach ($menus as $menu) {
            $label = $menu->title;
            if ((int) $menu->parent_id > 0 && isset($parentTitles[$menu->parent_id])) {
                $label = $parentTitles[$menu->parent_id] . ' › ' . $menu->title;
            }
            $options[] = (object) [
                'link' => $menu->link,
                'label' => $label,
            ];
            $links[] = $menu->link;
        }

        return ['options' => $options, 'links' => $links];
    }
}
