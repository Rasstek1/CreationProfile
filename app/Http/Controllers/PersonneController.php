<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personne;
use Session; // Laravel propose une classe pour gérer les sessions
use Storage; // Pour le stockage des fichiers

class PersonneController extends Controller
{

//Methode qui crée un nouveau profil et le stocke dans la session
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $request->validate([
            'nom' => 'required',
            'compteEnBanque' => 'required|numeric',
            'salaire' => 'required|numeric',
            'animaux' => 'required|integer',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Traitement du fichier photo
        $photo = $request->file('photo');
        $photoName = time() . '.' . $photo->getClientOriginalExtension();
        $targetPath = storage_path('app/uploads/') . $photoName;


        // Stockage du fichier
        Storage::disk('public')->put($targetPath, file_get_contents($photo));

        // Création d'une nouvelle instance de la classe Personne
        $personne = new Personne([
            'nom' => $request->input('nom'),
            'compteEnBanque' => $request->input('compteEnBanque'),
            'salaire' => $request->input('salaire'),
            'animaux' => $request->input('animaux'),
            'photo' => $photoName
        ]);

        // Enregistrement de la nouvelle instance (vous devez avoir une méthode save() dans votre modèle Personne)
        $personne->save();

        // Stockage des informations dans la session
        $profiles = Session::get('profiles', []);
        $profiles[] = $personne;
        Session::put('profiles', $profiles);

        // Redirection
        return redirect()->route('profile.index')->with('status', 'Profil créé avec succès');
    }


}
