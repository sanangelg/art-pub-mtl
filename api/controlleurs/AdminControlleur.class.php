<?php
/**
 * Class AdminControlleur
 * Gère la page d'accueil
 * 
 * @author Jonathan Martel
 * @version 1.0
 * @update 2019-06-10
 * @license Creative Commons BY-NC 3.0 (Licence Creative Commons Attribution - Pas d’utilisation commerciale 3.0 non transposé)
 * @license http://creativecommons.org/licenses/by-nc/3.0/deed.fr
 */
 

class AdminControlleur extends Controlleur 
{
	
	public function getAction(Requete $requete){
		$res = array();
		if($_SESSION["utilisateur"]["type_acces"] == "admin"){
			if(isset($requete->url_elements[0]) && $requete->url_elements[0] == "usagers"){
				if(isset($requete->url_elements[1]) && $requete->url_elements[1] == "sup"){
					$aData[] = $requete->url_elements[2];
					$string = $this->ArrayToString($aData);
					$this->supUsager($string);
					header("Location:/art-pub-mtl/api/admin/usagers");
				}else{
					$resUsagers = $this->getListeUsagersFun();
					$oVue = new AdminVue();
					$oVue->afficheListeUsagers($resUsagers, $msgErreur = "");
				}
			}else{
				$resArt = $this->getListeArtiste();
				$resOeu = $this->getListeOeuvre();
				$oVue = new AdminVue();
				$oVue->afficheAccueilAdmin($resArt, $resOeu);
			}
		}
		
		else{
			echo "tu n'est pas un admin";
		}
		
	}

	public function postAction(Requete $requete){

		//Validation supprimer Usagers avec le Checkbox
		if (isset($_POST['supp'])) {
			$msgErreur ="";
			if (isset($_POST['checks']) && is_array($_POST['checks'])) {
				$selected = array();
				$num_checks = count($_POST['checks']);
				foreach ($_POST['checks'] as $key => $value) {
						$selected[] = $value;
				}
			}
			if (empty($selected)){
				$msgErreur = 'Aucune usager sélectionné';
				$res = $this->getListeUsagersFun();
				$oVue = new AdminVue();
				$oVue->afficheListeUsagers($res, $msgErreur);
			}

			if($msgErreur == ""){
				$string = $this->ArrayToString($selected);
				$this->supUsager($string);
				header("Location:/art-pub-mtl/api/admin/usagers");
			}
		
		}    
		
	}

	protected function getListeArtiste(){
		$oArtiste = new Artiste();
		$aArtiste = $oArtiste->getListe();
		return $aArtiste;
	}

	protected function getListeOeuvre(){
		$oOeuvre = new Oeuvre();
		$aOeuvre = $oOeuvre->getListe();
		return $aOeuvre;
	}

	protected function getListeUsagersFun(){
		$oUsagers = new Usagers();
		$aUsagers = $oUsagers->getListe();
		return $aUsagers;
	}

	// Section Supprimer Usagers
	protected function supUsager($aData){
		$oUsagers = new Usagers();
		$aUsagers = $oUsagers->deleteUsager($aData);
	}

	protected function ArrayToString($aData){
		
		if($msgErreur == ""){
			$premier = true;

			foreach($aData as $id){
				if($premier == true){
					$res= "WHERE id_usager = ". $id;
				}
				else{
					$res .=" OR  id_usager = ". $id;
				}
				$premier = false;
			}
			return $res;
		}
	}

}
?>