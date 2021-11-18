<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ShowPosts extends Component
{
    use WithFileUploads;
    use WithPagination;
    //esto es una propiedad del componente la propiedad search
    public $search ="";
    public $post, $image, $identificador;
    public $cant="10";

    //estos son los eventos oyentes
    protected $listeners = ['render'=> 'render','delete'];

    //ordenando los parámetros de búsqueda
    public $sort="id";
    public $direction="desc";

    //abrir el modal    
    public $open_edit = false;

    //tiempo de carga
    public $readyToLoad=false;


    

    //pasando la URL 
    protected $queryString= [
       'cant'=>['except'=>'10'],
       'sort'=>['except'=>'id'],
       'direction'=>['except'=>'desc'],
       'search'=>['except'=>'']

    ];

    //pasandole el metodo rand al identificador
    public function mount()
    {
        $this->identificador =rand();
        $this->post =new Post();
    }

    //borrar la paginacion cuando se busca algo 


    public function updatingSearch(){
        $this->resetPage();
    }

    //incluir las reglas de validacion para sincrinizarla con las propiedades
    protected $rules = [
        'post.title' => 'required',
        'post.content' => 'required'
    ];


    public function render()
    {
        if ($this->readyToLoad) {
            $posts = Post::where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('content', 'like', '%'.$this->search.'%')
                    ->orderBy($this->sort, $this->direction)->paginate($this->cant);  
        }else{
            $posts=[];
        }
        
        return view('livewire.show-posts', compact('posts'));
    }


    //tiempo de carga de la pagina
    public function loadPosts(){
        $this->readyToLoad = true;
    }
    
    //ordenando los parámetros de búsqueda
    public function order($sort)
    {
        if ($this->sort==$sort) {
            if ($this->direction == 'desc') {
                $this->direction ='asc';
            } else {
                $this->direction = 'desc';
            }
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
    }
    // funcion que edita los datos
    public function edit(Post $post)
    {
        $this->post = $post;
        $this->open_edit = true;
    }

    public function update()
    {
        $this->validate();

        if ($this->image) {
            Storage::delete([$this->post->image]);
            $this->post->image = $this->image->store('posts');
        }
        $this->post->save();
        $this->reset(['open_edit','image']);
        $this->identificador = rand();
        $this->emit('alert', 'El post se actualizo satisfactoriamente');
    }

    public function delete(Post $post){
        $post->delete();
    }
}
