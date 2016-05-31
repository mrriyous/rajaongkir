<?php
/**
*
*		Ongkir Library by @alfisyahri_lbs
*		@version 1.0 
*		@link http://anjir.esy.es
*		
*		Origin API by @rajaongkir
*		
*		Date Created 30.05.2016
*	
* 		@read 	this library is used for get province, city, and delivery prices
*				you can use this free, but maximum weight is 30KG . if you want more than that
*				you have to pay for being a premium user.
*				more details at : @link http://www.rajaongkir.com
*
*		HOW TO GET THE @var $key 
*		@steps :
*			1.	@link visit http://www.rajaongkir.com
*			2. 	Register a new account.
*			3. 	Get your key at your account page.
*		
*		HOW TO USE
*		@documentation :
*			1. 	Starter :
*				- 	@link : http://www.rajaongkir.com/dokumentasi/starter
*				-	@apiurl : http://api.rajaongkir.com
*			2. 	Basic :
*				- 	@link : http://www.rajaongkir.com/dokumentasi/basic
*				-	@apiurl : http://api.rajaongkir.com/basic
*			2. 	Pro :
*				- 	@link : http://www.rajaongkir.com/dokumentasi/pro
*				-	@apiurl : http://pro.rajaongkir.com/api
*/

class Ongkir{

	private $key = "8365d6bba34ff73da50f6c2196c4cbaa";

	protected $url = "http://api.rajaongkir.com/starter";

	protected $origin = 278; // 276 is city id for Medan, Change the origin with your city ID.

	protected $province;

	protected $city;

	protected $cost;

	protected $value = ['province','city','cost'];

	public function province($id=null)
	{
		$url = $this->url."/province".((!! $id) ? "?id=".$id : "");

		$province=$this->req($url);
		$this->setProvince($province);

		return $this;
	}

	public function city($province=null,$id=null)
	{
		$url = $this->url."/city".((!! $province) ? "?province=".$province : "");
		
		if($id !== null):
			$url.=($province !== null) ? "&" : "?";
			$url.="id=".$id;
		endif;

		$city=$this->req($url);
		$this->setCity($city);

		return $this;
	}

	public function cost(array $prop)
	{
		$field="origin=".$this->origin;

		foreach ($prop as $key => $value) {
			$field.="&".$key."=".$value;
		}

		$cost = $this->req($this->url."/cost",true,$field);
		$this->setCost($cost);
		return $this;
	}

	public function setProvince($p){
		$this->province = json_decode($p)->rajaongkir->results;
		$this->value['province'] = $this->province;
	}

	public function setCity($p){
		$this->city = json_decode($p)->rajaongkir->results;
		$this->value['city'] = $this->city;
	}
	public function setCost($p){
		$this->cost = json_decode($p)->rajaongkir->results;
		$this->value['cost'] = $this->cost;
	}

	public function get($str){
		return $this->value[$str];
	}

	public function req($url,$cost=false,$field=null)
	{
		
		$curl_default_=[
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 300,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		];

		$curl_http_header_=[
		    	"key: ".$this->key,
			];

		$curl_request_ = ($cost) ? "POST" : "GET";
		$curl_default_[CURLOPT_CUSTOMREQUEST] = $curl_request_;
		
		if($cost):
			array_push($curl_http_header_, "content-type: application/x-www-form-urlencoded");
			$curl_default_[CURLOPT_POSTFIELDS ] = $field;
		endif; 
		$curl_default_[CURLOPT_HTTPHEADER] = $curl_http_header_;


		$curl = curl_init();

		curl_setopt_array($curl, $curl_default_);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) :
			throw new Exception("Curl error #".$err);
		else:
		  	return $response;
		endif;
	}

}