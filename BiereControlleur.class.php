<?php

/**
 * Class BiereControleur
 * Controleur de la ressource Biere
 * 
 * @author Jonathan Martel
 * @version 1.1
 * @update 2019-11-11
 * @license MIT
 */


class BiereControlleur
{
	private $retour = array('data' => array());

	/**
	 * Méthode qui gère les action en GET
	 * @param Requete $oReq
	 * @return Mixed Données retournées
	 */
	public function getAction(Requete $oReq)
	{
		if (isset($oReq->url_elements[0]) && is_numeric($oReq->url_elements[0]))	// Normalement l'id de la biere 
		{
			$id_biere = (int)$oReq->url_elements[0];

			if (isset($oReq->url_elements[1])) {
				switch ($oReq->url_elements[1]) {
					case 'commentaire':
						$this->retour["data"] = $this->getCommentaire($id_biere);
						break;
					case 'note':
						$this->retour["data"] = $this->getNote($id_biere);
						break;
					default:
						$this->retour['erreur'] = $this->erreur(400);
						unset($this->retour['data']);
						break;
				}
			} else // Retourne les infos d'une bière
			{
				$this->retour["data"] = $this->getBiere($id_biere);
			}
		} else {
			$this->retour["data"] = $this->getListeBiere();
		}

		return $this->retour;
	}

	/**
	 * Méthode qui gère les action en POST
	 * @param Requete $oReq
	 * @return Mixed Données retournées
	 */
	public function postAction(Requete $oReq)	// Modification
	{
		if (!$this->valideAuthentification()) {
			$this->retour['erreur'] = $this->erreur(401);
		} else {

			if (isset($oReq->url_elements[0]) && is_numeric($oReq->url_elements[0])) // Normalement l'id de la bière
			{
				$id_biere = (int)$oReq->url_elements[0];
				$this->retour["data"] = $this->postBiere($id_biere, $oReq->parametres);
			}
		}
		return $this->retour;
	}

	/**
	 * Méthode qui gère les action en PUT
	 * @param Requete $oReq
	 * @return Mixed Données retournées
	 */
	public function putAction(Requete $oReq)		//ajout ou modification
	{
		//var_dump($oReq);
		if (!$this->valideAuthentification()) {
			$this->retour['erreur'] = $this->erreur(401);
		} else {
			if (isset($oReq->url_elements[0]) && is_numeric($oReq->url_elements[0])) // Normalement l'id de la bière
			{
				$id_biere = (int)$oReq->url_elements[0];

				if (isset($oReq->url_elements[1])) {
					switch ($oReq->url_elements[1]) {
						case 'commentaire':
							$this->retour["data"] = $this->setCommentaire($id_biere, $oReq->parametres);
							break;
						case 'note':
							$this->retour["data"] = $this->setNote($id_biere, $oReq->parametres);
							break;
						default:
							$this->retour['erreur'] = $this->erreur(400);
							unset($this->retour['data']);
							break;
					}
				}
			} else {

				$this->retour["data"] = $this->setBiere($oReq->parametres);
			}
		}

		return $this->retour;
	}

	/**
	 * Méthode qui gère les action en DELETE
	 * @param Requete $oReq
	 * @return Mixed Données retournées
	 */
	public function deleteAction(Requete $oReq)
	{
		if (!$this->valideAuthentification()) {
			$this->retour['erreur'] = $this->erreur(401);
		} else {
			if (isset($oReq->url_elements[0]) && is_numeric($oReq->url_elements[0])) // Normalement l'id de la bière
			{

				$id_biere = (int)$oReq->url_elements[0];
				$this->retour["data"] = $this->deleteBiere($id_biere);
			}
		}

		return $this->retour;
	}

	/**
	 * Retourne les informations de la bière $id_biere
	 * @param int $id_biere Identifiant de la bière
	 * @return Array Les informations de la bière
	 * @access private
	 */
	private function getBiere($id_biere)
	{
		$res = array();
		$oBiere = new Biere();
		$res = $oBiere->getBiere($id_biere);
		return $res;
	}
	/**
	 * Ajouter biere
	 * @access private
	 */
	private function setBiere($donnees)
	{
		$res = array();

		$oBiere = new Biere();
		$res = $oBiere->ajouterBiere($donnees);

		return $res;
	}
	/**
	 * Modification biere
	 * @access private
	 */
	private function postBiere($id_biere, $donnees)
	{
		$res = array();

		$oBiere = new Biere();
		$res = $oBiere->modifierBiere($id_biere, $donnees);

		return $res;
	}
	/**
	 * Retourne les informations des bières de la db	 
	 * @return Array Les informations sur toutes les bières
	 * @access private
	 */
	private function getListeBiere()
	{
		$res = array();
		$oBiere = new Biere();
		$res = $oBiere->getListe();

		return $res;
	}
	/**
	 * Supprimer les informations de bière de la db par id de biere
	 * @access private
	 */
	private function deleteBiere($id_biere)
	{
		$res = array();
		$oBiere = new Biere();
		$res = $oBiere->effacerBiere($id_biere);

		return $res;
	}
	/**
	 * Retourne les commentaires de la bière $id_biere
	 * @param int $id_biere Identifiant de la bière
	 * @return Array Les commentaires de la bière
	 * @access private
	 */
	private function getCommentaire($id_biere)
	{
		$res = array();
		return $res;
	}
	/**
	 * Ajouter ou modifier commentaire
	 * @access private
	 */
	private function setCommentaire($id_biere, $donnees)
	{
		$res = array();

		$oUsager = new Usager();
		$id_usager = $oUsager->ajouterUsager($donnees['courriel']);
		var_dump($id_biere);
		$oCommentaire = new Commentaire();
		$res = $oCommentaire->ajouterCommentaire($id_usager, $id_biere, $donnees['commentaire']);

		return $res;
	}
	/**
	 * Retourne la note moyenne et le nombre de note de la bière $id_biere
	 * @param int $id_biere Identifiant de la bière
	 * @return Array La note de la bière
	 * @access private
	 */
	private function getNote($id_biere)
	{
		$res = array();
		$oNote = new Note();
		$res = $oNote->getMoyenne($id_biere);
		return $res;
	}
	/**
	 * Ajouter ou modifier note
	 * @access private
	 */
	private function setNote($id_biere, $donnees)
	{
		$res = array();

		$oUsager = new Usager();
		$id_usager = $oUsager->ajouterUsager($donnees['courriel']);
		var_dump($id_biere);
		$oNote = new Note();
		$res = $oNote->ajouterNote($id_usager, $id_biere, $donnees['note']);

		return $res;
	}

	/**
	 * Valide les données d'authentification du service web
	 * @return Boolean Si l'authentification est valide ou non
	 * @access private
	 */
	private function valideAuthentification()
	{
		$access = false;
		$headers = apache_request_headers();

		if (isset($headers['Authorization'])) {
			if (isset($_SERVER['PHP_AUTH_PW']) && isset($_SERVER['PHP_AUTH_USER'])) {
				if ($_SERVER['PHP_AUTH_PW'] == 'biero' && $_SERVER['PHP_AUTH_USER'] == 'biero') {
					$access = true;
				}
			}
		}
		return $access;
	}


	private function erreur($code, $data = "")
	{
		//header('HTTP/1.1 400 Bad Request');
		http_response_code($code);

		return array("message" => "Erreur de requete", "code" => $code);
	}
}
