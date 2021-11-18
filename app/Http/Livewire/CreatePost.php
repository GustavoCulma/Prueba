<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Post;
//imagen 
use Livewire\WithFileUploads;



class CreatePost extends Component
{

    //imagen 
    use WithFileUploads;
    //propiedad para abirr el boton 
    public $open = false;
    
    //identificador para que el input file cambie por un nuevo input
    public $title, $content, $image, $identificador;

    public function mount(){
        $this->identificador = rand();
    }

    //reglas de validacion. 
    protected $rules =[
        'title'=>'required',
        'content'=>'required',
        'image' => 'required|image|max:2048'
    ];


    // aqui esta salvando lo viene del modal
    public function save (){

        //aplicando las reglas de validacion
        $this->validate();
        
        //imagen 
        $image = $this->image->store('post');
        Post::create([
            'title'=>$this->title,
            'content'=>$this->content,
            'image' => $image
        ]);

        //emitir un evento oyente: y aqui lo envia hacia la vista 
        $this->emitTo('show-posts','render'); 
        $this->emit('alert', 'El post se creo satisfactoriamente'); 

        //borar el contenido despues de guaradardo
        $this->reset('open', 'title', 'content', 'image');
        $this->identificador = rand();

        
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
