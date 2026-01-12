<?php defined('BASEPATH') || exit('No direct script access allowed');
/**
 * @Author  : Suwito
 * @Email   : suwito.lt@gmail.com
 * @Date    : 2017-01-26 13:36:42
 * @Last Modified by    : Yunaz
 * @Last Modified time  : 2017-01-26 22:15:59
 */

/**
 * A simple helper method for checking menu items against the current class/controller.
 * This function copied from cibonfire
 * <code>
 *   <a href="<?php echo site_url(SITE_AREA . '/content'); ?>" <?php echo check_class(SITE_AREA . '/content'); ?> >
 *    Admin Home
 *  </a>
 *
 * </code>
 *
 * @param string $item       The name of the class to check against.
 * @param bool   $class_only If true, will only return 'active'. If false, will
 * return 'class="active"'.
 *
 * @return string Either 'active'/'class="active"' or an empty string.
 */
function check_class($item = '', $class_only = false)
{
    if (strtolower(get_instance()->router->class) == strtolower($item)) {
        return $class_only ? 'active' : 'class="active"';
    }

    return '';
}

/**
 * A simple helper method for checking menu items against the current method
 * (controller action) (as far as the Router knows).
 *
 * @param string    $item       The name of the method to check against. Can be an array of names.
 * @param bool      $class_only If true, will only return 'active'. If false, will return 'class="active"'.
 *
 * @return string Either 'active'/'class="active"' or an empty string.
 */
function check_method($item, $class_only = false)
{
    $items = is_array($item) ? $item : array($item);
    if (in_array(get_instance()->router->method, $items)) {
        return $class_only ? 'active' : 'class="active"';
    }

    return '';
}

/**
 * Check if the logged user has permission or not
 * @param string $permission_name
 * @return bool True if has permission and false if not
 */
function has_permission($permission_name = "")
{
    $ci =& get_instance();

	$return = $ci->auth->has_permission($permission_name);

	return $return;
}

/**
 * @param  string $kode_tambahan
 * @return string generated code
 */
 
function gen_primary($kode_tambahan = "")
{

    $CI     		=& get_instance();

    $tahun          = intval(date('Y'));
    $bulan          = intval(date('m'));
    $hari           = intval(date('d'));
    $jam            = intval(date('H'));
    $menit          = intval(date('i'));
    $detik          = intval(date('s'));
    $temp_ip        = ($CI->input->ip_address()) == "::1" ? "127.0.0.1" : $CI->input->ip_address();
    $temp_ip        = explode(".", $temp_ip);
    $ipval          = $temp_ip[0] + $temp_ip[1] + $temp_ip[2] + $temp_ip[3];

    $kode_rand      = mt_rand(1,1000)+$ipval;
    $letter1        = chr(mt_rand(65,90));
    $letter2        = chr(mt_rand(65,90));

    $kode_primary   = $tahun.$bulan.$hari.$jam.$menit.$detik.$letter1.$kode_rand.$letter2;

    return $kode_tambahan . $kode_primary;
}


if(! function_exists('gen_idcustomer'))
{
function gen_idcustomer($kode_tambahan = "")
{

    $CI     =& get_instance();
    $CI->load->model('Customer/Customer_model');

    $query = $CI->Customer_model->generate_id($kode_tambahan);
    if(empty($query)){
        return 'Error';
    }else{
        return $query;
    }
}
}

if(! function_exists('gen_id_toko'))
{
function gen_id_toko($kode_tambahan = "")
{

    $CI     =& get_instance();
    $CI->load->model('Customer/Toko_model');

    $query = $CI->Toko_model->generate_id($kode_tambahan);
    if(empty($query)){
        return 'Error';
    }else{
        return $query;
    }
}
}

if(! function_exists('get_id_pnghn'))
{
function get_id_pnghn($kode_tambahan = "")
{

    $CI     =& get_instance();
    $CI->load->model('Customer/Penagihan_model');

    $query = $CI->Penagihan_model->generate_id($kode_tambahan);
    if(empty($query)){
        return 'Error';
    }else{
        return $query;
    }
}
}

if(! function_exists('get_id_pmbyr'))
{
function get_id_pmbyr($kode_tambahan = "")
{

    $CI     =& get_instance();
    $CI->load->model('Customer/Pembayaran_model');

    $query = $CI->Pembayaran_model->generate_id($kode_tambahan);
    if(empty($query)){
        return 'Error';
    }else{
        return $query;
    }
}
}

if(! function_exists('get_id_pic'))
{
function get_id_pic($kode_tambahan = "")
{

    $CI     =& get_instance();
    $CI->load->model('Customer/Pic_model');

    $query = $CI->Pic_model->generate_id($kode_tambahan);
    if(empty($query)){
        return 'Error';
    }else{
        return $query;
    }
}
}

if(! function_exists('gen_idsupplier'))
{
function gen_idsupplier($kode_tambahan = "")
{

    $CI     =& get_instance();
    $CI->load->model('Supplier/Supplier_model');

    $query = $CI->Supplier_model->generate_id($kode_tambahan);
    if(empty($query)){
        return 'Error';
    }else{
        return $query;
    }
}
}


if(! function_exists('simpan_aktifitas'))
{
    function simpan_aktifitas($nm_hak_akses = "", $kode_universal = "", $keterangan ="", $jumlah = 0, $sql = "", $status = NULL)
    {
        $CI     =& get_instance();

        $CI->load->model('aktifitas/aktifitas_model');

        $result = $CI->aktifitas_model->simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);

        return $result;
    }
}

/*
* $date_from is the date with format dd/mm/yyyy H:i:s / dd/mm/yyyy
*/
if (! function_exists('date_ymd')) {
    function date_ymd($date_from)
    {
        $error = false;
        if(strlen($date_from) <= 10){
            list($dd,$mm,$yyyy) = explode('/',$date_from);

            if (!checkdate(intval($mm),intval($dd),intval($yyyy)))
            {
                    $error = true;
            }
        }
        else
        {
            list($dd,$mm,$yyyy) = explode('/',$date_from);
            list($yyyy,$hhii) = explode(" ", $yyyy);

            if (!checkdate($mm,$dd,$yyyy))
            {
                    $error = true;
            }
        }

        if($error)
        {
            return false;
        }

        if(strlen($date_from) <= 10)
        {
            $date_from = DateTime::createFromFormat('d/m/Y', $date_from);
            $date_from = $date_from->format('Y-m-d');
        }
        else
        {
            $date_from = DateTime::createFromFormat('d/m/Y H:i', $date_from);
            $date_from = $date_from->format('Y-m-d H:i');
        }

        return $date_from;
    }
}

if(! function_exists('simpan_alurkas')){

    function simpan_alurkas($kode_accountKas = null, $ket = "", $total = null , $status = null, $nm_hak_akses = ""){

        $CI     =& get_instance();

        $CI->load->model('kas/kas_model');

        $result = $CI->kas_model->simpan_alurKas($kode_accountKas, $ket, $total, $status, $nm_hak_akses);

        return $result;

    }

}

if(! function_exists('buatrp')){
    function buatrp($angka)
    {
     $jadi =number_format($angka,0,',','.');
    return $jadi;
    }
}

if(! function_exists('formatnomor')){
    function formatnomor($angka)
    {
     if($angka){
         $jadi = number_format($angka,0,',','.');
         return $jadi;
        }
    }
}

if(!function_exists('ynz_terbilang_format')){
    function ynz_terbilang_format($x) {
        $x = abs($x);
        $angka = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($x <12) {
            $temp = " ". $angka[$x];
        } else if ($x <20) {
            $temp = ynz_terbilang_format($x - 10). " belas";
        } else if ($x <100) {
            $temp = ynz_terbilang_format($x/10)." puluh". ynz_terbilang_format($x % 10);
        } else if ($x <200) {
            $temp = " seratus" . ynz_terbilang_format($x - 100);
        } else if ($x <1000) {
            $temp = ynz_terbilang_format($x/100) . " ratus" . ynz_terbilang_format($x % 100);
        } else if ($x <2000) {
            $temp = " seribu" . ynz_terbilang_format($x - 1000);
        } else if ($x <1000000) {
            $temp = ynz_terbilang_format($x/1000) . " ribu" . ynz_terbilang_format($x % 1000);
        } else if ($x <1000000000) {
            $temp = ynz_terbilang_format($x/1000000) . " juta" . ynz_terbilang_format($x % 1000000);
        } else if ($x <1000000000000) {
            $temp = ynz_terbilang_format($x/1000000000) . " milyar" . ynz_terbilang_format(fmod($x,1000000000));
        } else if ($x <1000000000000000) {
            $temp = ynz_terbilang_format($x/1000000000000) . " trilyun" . ynz_terbilang_format(fmod($x,1000000000000));
        }
        return $temp;
    }
}

if (!function_exists('ynz_terbilang')) {
    function ynz_terbilang($x, $style=1) {
        if($x<0) {
            $hasil = "minus ". trim(ynz_terbilang_format($x));
        } else {
            $hasil = trim(ynz_terbilang_format($x));
        }
        switch ($style) {
        case 1:
            $hasil = strtoupper($hasil);
            break;
        case 2:
            $hasil = strtolower($hasil);
            break;
        case 3:
            $hasil = ucwords($hasil);
            break;
        default:
            $hasil = ucfirst($hasil);
            break;
        }
        return $hasil;
    }
}

if(! function_exists('tipe_pengiriman')){
    function tipe_pengiriman($ket=false){
        $uu = array(
            'SENDIRI' => 'MILIK SENDIRI', 
            'SEWA' => 'SEWA',
            'EKSPEDISI' => 'EKSPEDISI',
            'PELANGGAN' => 'PELANGGAN AMBIL SENDIRI'
            );
        if($ket ==  true){
            return $uu[$ket];
        } else {
            return $uu;
        }
    }
}

if(! function_exists('selisih_hari')){
    function selisih_hari($tgl,$now){
        $aw = new DateTime($tgl);
        $ak = new DateTime($now);
        $interval = $aw->diff($ak);
        return $interval->days;
    }
}

if(! function_exists('kategori_umur_piutang')){
    function kategori_umur_piutang($ket=false){
        $uu = array(
            '0|14'  => '0-14', 
            '15|29' => '15-29',
            '30|59' => '30-59',
            '60|89' => '60-89',
            '90'    => '>90',
            );
        if($ket ==  true){
            return $uu[$ket];
        } else {
            return $uu;
        }
    }
}

if( ! function_exists('the_bulan'))
{
    function the_bulan($time=false){
        $a = array ('01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );
        return $time == false ? $a : $a[$time];
    }
}

if(! function_exists('is_jenis_bayar')){
    function is_jenis_bayar($ket=false){
        $uu = array(
            'CASH' => 'CASH', 
            'TRANSFER' => 'TRANSFER',
            'BG' => 'GIRO'
            );
        if($ket ==  true){
            return $uu[$ket];
        } else {
            return $uu;
        }
    }
}

if(! function_exists('is_status_giro')){
    function is_status_giro($ket=false){
        $uu = array(
            'OPEN' => 'OPEN', 
            'INV' => 'INVOICE',
            'CAIR' => 'CAIR',
            'TOLAK' => 'TOLAK'
            );
        if($ket ==  true){
            return $uu[$ket];
        } else {
            return $uu;
        }
    }
}

function tahun($tgl){
			$tahun = substr($tgl,0,4);
			return $tahun;		 
	}	
	
	function tgl_indo($tgl){
			$tanggal = substr($tgl,8,2);
			$bulan =substr($tgl,5,2);
			$tahun = substr($tgl,0,4);
			return $tanggal.'/'.$bulan.'/'.$tahun;		 
	}	

	function getBulan($bln){
				switch ($bln){
					case 1: 
						return "Januari";
						break;
					case 2:
						return "Februari";
						break;
					case 3:
						return "Maret";
						break;
					case 4:
						return "April";
						break;
					case 5:
						return "Mei";
						break;
					case 6:
						return "Juni";
						break;
					case 7:
						return "Juli";
						break;
					case 8:
						return "Agustus";
						break;
					case 9:
						return "September";
						break;
					case 10:
						return "Oktober";
						break;
					case 11:
						return "November";
						break;
					case 12:
						return "Desember";
						break;
				}
			} 
			
		function tgl_indojam($tgl){
			$tanggal2 = substr($tgl,8,2);
			$bulan2 =substr($tgl,5,2);
			$tahun2 = substr($tgl,0,4);
			$jam = substr($tgl,11,2);
			$menit = substr($tgl,14,2);
			
			return $tanggal2.'/'.$bulan2.'/'.$tahun2.' '.$jam.':'.$menit;		 
		}
		
		function jin_date_sql($date){
	$exp = explode('/',$date);
	if(count($exp) == 3) {
		$date = $exp[2].'-'.$exp[1].'-'.$exp[0];
	}
	return $date;
}
 
function jin_date_str($date){
	$exp = explode('-',$date);
	if(count($exp) == 3) {
		$date = $exp[2].'/'.$exp[1].'/'.$exp[0];
	}
	return $date;
}

function periode($tgl){
	
			$tanggal = substr($tgl,8,2);
			$bulan =substr($tgl,5,2);
			$tahun = substr($tgl,0,4);
			return $tanggal.'/'.$bulan.'/'.$tahun;		 
	}	

function tgl_excel($tgl){	
	
$excell_date= $tgl;

// $base_day dikurangkan 1 untuk mendapatkan timestamp yang tepat
$base_timestamp = mktime(0,0,0,1,$excell_date-1,1900);

// Output: 01-01-1970:
$date= date("d-m-Y",$base_timestamp);
return $date;
}

function combo_p_bulan($val=0){
	$combo='<option value="">Pilih Bulan</option>';
	for ($x = 1; $x <= 12; $x+=(0.5)) {
		$combo.='<option value="'.$x.'"'.($val==$x?' selected':'').'>'.$x.' bulan</option>';
	}
	return $combo;
}

function combo_p_hari($val=0){
	$combo='<option value="">Pilih Hari</option>';
	for ($x = 1; $x <= 29; $x++) {
		$combo.='<option value="'.$x.'"'.($val==$x?' selected':'').'>'.$x.' hari</option>';
	}
	return $combo;
}

function status_pr($idh,$idd,$tipe,$status,$tipe_status,$links){
	$ret='';
	if(stripos($tipe,'PP')!==false) {
		if($status==0) {
			echo '<a href="'.base_url($links.$idh.'/'.$idd).'" class="label bg-green">Persyaratan Pembayaran</a> ' ;
		}
	}
	if(stripos($tipe,'PO')!==false) {
		if($status==0) {
			echo '<label><input type="checkbox" name="idkomponen[]" value="'.$idd.'" id="iddtl_'.$idd.'" /> Buat PO</label>';
		}
	}
	if(stripos($tipe,'SC')!==false) {
		if($status==0) {
			echo '<label><input type="checkbox" name="idkomponen[]" value="'.$idd.'" id="iddtl_'.$idd.'" /> Buat PO</label>
			<a href="'.base_url($links.$idh.'/'.$idd).'" class="label bg-yellow pull-right">In House</a> ';
		}
	}
	if(stripos($tipe,'KB')!==false) {
		if($status==0) {
			echo ' <label><input type="checkbox" name="idkomponenkasbon[]" value="'.$idd.'" id="iddtl_'.$idd.'" /> Buat Cash Advance</label>';
		}
	}
	if($status>0) {
		echo 'Sudah dibuat '.(($tipe_status=='KASBON'?'Cash Advance':$tipe_status));
	}
	return $ret;
}

    
