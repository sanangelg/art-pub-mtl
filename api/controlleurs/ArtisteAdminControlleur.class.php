<?php
/**
 * Controlleur de la ressource Artiste
 * 
 * 
 * @author Jonathan Martel
 * @version 1.0
 * @update 2016-11-16
 * @license Creative Commons BY-NC 3.0 (Licence Creative Commons Attribution - Pas d’utilisation commerciale 3.0 non transposé)
 * @license http://creativecommons.org/licenses/by-nc/3.0/deed.fr
 */
 

class ArtisteAdminControlleur extends Controlleur 
{

	public function getAction(Requete $requete)
	{
		$res = array();
		$msgErreur="";
		 // si sup on regarde l'id et on supprime
		if(isset($requete->url_elements[0]) && $requete->url_elements[0] == "sup"){
			if(isset($_SESSION["utilisateur"]) && $_SESSION["utilisateur"]["type_acces"] == "admin"){
				$aData[] = $requete->url_elements[1];
				$string = $this->ArrayToString($aData);
				$this->supArtiste($string);
				header("Location:/art-pub-mtl/api/artisteAdmin");
				
				
				// $this->supArtiste((int)$requete->url_elements[1]);
				// $res = $this->getListeArtiste();
				
			}
			else{
				echo "vous devez être connecté en tant qu'admin pour pouvoir supprimer";
			}	
		}
		else if(isset($requete->url_elements[0]) && $requete->url_elements[0] == "mod"){
			if(isset($_SESSION["utilisateur"]) && $_SESSION["utilisateur"]["type_acces"] == "admin"){
				$aData = $this->getArtiste((int)$requete->url_elements[1]);
				$this->getFormModif($aData, $msgErreur="");
				
				
			}
			else{
				echo "vous devez être connecté en tant qu'admin pour pouvoir supprimer";
			}	
		}
		else if(isset($requete->url_elements[0]) && $requete->url_elements[0] == "ajouter"){
			if(isset($_SESSION["utilisateur"]) && $_SESSION["utilisateur"]["type_acces"] == "admin"){
				$this->getFormAjout($msgErreur);				
			}
			else{
				echo "vous devez être connecté en tant qu'admin pour pouvoir supprimer";
			}
		}
		// Liste des artistes
        else 	
        {
			$res = $this->getListeArtiste();
			$oVue = new AdminVue;
			$oVue->afficheArtistes($res, $msgErreur = "");
			
		}
	}

	public function postAction(Requete $requete){

		if(isset($requete->url_elements[0]) && $requete->url_elements[0] == "ajouter"){
			if(isset($requete->url_elements[1]) && $requete->url_elements[1] == "insert"){
				$msgErreur ="";
				if(empty(trim($_POST["nom_collectif"]))){
					if(empty(trim($_POST["nom"])) && empty(trim($_POST["prenom"]))){
						$msgErreur.= "Vous devez remplir le champ nom collectif ou nom et prenom. <br>";
					}
					else if(empty(trim($_POST["nom"])) || empty(trim($_POST["prenom"]))){
						$msgErreur.= "Vous devez remplir le champ prenom et prénom ou un nom collectif. <br>";
					}
				}
				if(empty($_POST["biographie"])){
					$msgErreur .= "Vous devez remplir le champ biographie. <br>";
				}

				// Si le message d'erreur est vide on lance l'ajout, sinon on affiche le message
				if($msgErreur == ""){
					$aData = Array();
					foreach($_POST as $cle=>$value){
						$aData[$cle] = $value;
					}
						$this->AjouterData($aData);
						header("Location: /art-pub-mtl/api/artisteAdmin");
				}
				else{
					
					$this->getFormAjout($msgErreur);
				}
			}
		}
		else if(isset($requete->url_elements[0]) && $requete->url_elements[0] == "mod"){
			if(isset($requete->url_elements[1]) && $requete->url_elements[1] == "insert"){
				$msgErreur ="";
				if(empty(trim($_POST["nom_collectif"]))){
					if(empty(trim($_POST["nom"])) && empty(trim($_POST["prenom"]))){
						$msgErreur.= "Vous devez remplir le champ nom collectif ou nom et prenom. <br>";
					}
					else if(empty(trim($_POST["nom"])) || empty(trim($_POST["prenom"]))){
						$msgErreur.= "Vous devez remplir le champ prenom et prénom ou un nom collectif. <br>";
					}
				}
				if(empty($_POST["biographie"])){
					$msgErreur .= "Vous devez remplir le champ biographie. <br>";
				}

				if($msgErreur == ""){
					$aData = Array();
					foreach($_POST as $cle=>$value){
						$aData[$cle] = $value;
					}
					$this->modifData($aData, $msgErreur);
					header("Location: /art-pub-mtl/api/artisteAdmin");
				}
				else{
					$this->getFormModif($_POST, $msgErreur);
				}
			}
		}
		//Validation supprimer avec le Checkbox
		else if (isset($_POST['suppArt'])) {
			$msgErreur ="";
			if (isset($_POST['checks']) && is_array($_POST['checks'])) {
				$selected = array();
				$num_checks = count($_POST['checks']);
				foreach ($_POST['checks'] as $key => $value) {
						$selected[] = $value;
				}
			}
			if (empty($selected)){
				$msgErreur = 'Aucune artiste sélectionné';
				$res = $this->getListeArtiste();
				$oVue = new AdminVue();
				$oVue->afficheArtistes($res, $msgErreur);
			}

			if($msgErreur == ""){
				$string = $this->ArrayToString($selected);
				$this->supArtiste($string);
				header("Location:/art-pub-mtl/api/artisteAdmin");
			}
		
		}
	}


	// Section Artistes
	protected function getListeArtiste(){
		$oArtiste = new Artiste();
		$aArtiste = $oArtiste->getListe();
		return $aArtiste;
	}

	protected function getArtiste($id_artiste){
		$oArtiste= new Artiste();
		$aArtiste = $oArtiste->getArtiste($id_artiste);		
		return $aArtiste;
	}

	// Section Ajouter
	protected function getFormAjout(){
		$oVue = new AdminVue();
		$oVue->getFormAjoutArtiste();
	}

	protected function AjouterData($aData){
		$oArtiste = new Artiste();
		$oArtiste->AjouterArtiste($aData);
	}
	
	// Section Modifier
	protected function getFormModif($aData, $msgErreur){
		$oVue = new AdminVue();
		$oVue->getFormModifArtiste($aData, $msgErreur);
	}

	protected function modifData($aData){
		$oArtiste = new Artiste();
		$oArtiste->modifierArtiste($aData);
	}
	
	// Section Supprimer
	protected function supArtiste($aData){
		$oArtiste= new Artiste();
		$aArtiste = $oArtiste->deleteArtiste($aData);
	}

	protected function ArrayToString($aData){
		
		if($msgErreur == ""){
			$premier = true;

			foreach($aData as $id){
				if($premier == true){
					$res= "WHERE id_artiste = ". $id;
				}
				else{
					$res .=" OR  id_artiste = ". $id;
				}
				$premier = false;
			}
			return $res;
		}
	}
}
?>