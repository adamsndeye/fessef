<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use DB;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $count=DB::table('messages')->where('user_to',Auth::user()->id)->where('statut',1)->count();
       

            $authuserid=Auth::user()->id;
            $alluser1= DB::table('users')
            ->join('conversations','users.id','conversations.user_one')
            ->where('conversations.user_two',Auth::user()->id)->get();
    
            $alluser2= DB::table('users')
            ->join('conversations','users.id','conversations.user_two')
            ->where('conversations.user_one',Auth::user()->id)->get();
            $allconversations= array_merge( $alluser1->toArray(),$alluser2->toArray());
            
           
            if(empty($allconversations[0]->id)) {
              return redirect()->route('home');
            }else{
             $id=$allconversations[0]->id;
             return Inertia::render('Message/Index', [
                 'allconversations' => $allconversations,
                 
                 'id' => $id,
                 'authuserid' => $authuserid,
                 'count' => $count,
            //     'messsender' => $messsend,
            //     'messreceiver' => $messreceiver,
            //     'userid' => $userid,
            ]);
        }
        
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {

        // Message::create([
        //     'sender_id' => $request->sender_id,
        //     'receiver_id' => $request->receiver_id,
        //     'messages' => $request->messages,
        //     'user_id' => Auth::user()->id,
        // ]);
        // return back();
    }

    /**
     * Display the specified resource.
     *
     * @param Message $message
     * @return RedirectResponse|Response|\Inertia\Response
     */
    public function show(Request $request, $id)

    {
            
     DB::table('messages')->where('user_to',Auth::user()->id)->where('conversation_id',$id)->where('statut',1)->update(['statut' =>0]);  

        $authuserid=Auth::user()->id;
        $alluser1= DB::table('users')
        ->join('conversations','users.id','conversations.user_one')
       
        ->where('conversations.user_two',Auth::user()->id) ->get();
        
        
        $alluser2= DB::table('users')
        ->join('conversations','users.id','conversations.user_two')
        
        ->where('conversations.user_one',Auth::user()->id)->get();
        
        $allconversations= array_merge( $alluser1->toArray(),$alluser2->toArray());
        // $id=$allconversations[0]->id;
        
        $getmessages= DB::table('messages')
         ->join('users','users.id','messages.user_from')
         
         ->where('messages.conversation_id',$id)
         ->orderBy('messages.id','asc')->get();
          
        
        return Inertia::render('Message/Show', [
            'allconversations' => $allconversations,
            'getmessages' => $getmessages,
            'id' => $id,
            'authuserid' => $authuserid,
            
       //     'messsender' => $messsend,
       //     'messreceiver' => $messreceiver,
       //     'userid' => $userid,
       ]);
        
        // $messsend = Message::with('user')->where('sender_id', Auth::user()->id)->get();

        // $messreceiver = Message::with('user')->where('receiver_id', Auth::user()->id)->where('statut', 1)->get();
        // Message::where('sender_id', $id)->update(['isRead' => 1]);

        // $userid = Auth::user()->id;
        // $messages = Message::with('user')->where('statut', 1)->get();
        // $users = Message::with('user')->where('user_id', '!=', Auth::user()->id)->get();

        // return Inertia::render('Message/Show', [
        //     'messages' => $messages,
        //     'userid' => $userid,
        //     'users' => $users,
        //     'messsender' => $messsend,
        //     'messreceiver' => $messreceiver,
        // ]);
        // return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Message $message
     * @return Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Message $message
     * @return Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Message $message
     * @return Response
     */
    public function updatestatut(Message $message, $id)
    {
        // Message::where('id', $id)->update(['statut' => 0]);
        // return back();
    }
    public function destroy(Message $message, $id)
    {
        // Message::where('id', $id)->delete();
        // return back();
    }

    public function contact(Request $request)
    {
        

        $message= $request->message;
        $userid=Auth::user()->id;
        $userto=$request->user_to;
        

        $chekcon1=DB::table('conversations')->where('user_one',$userid)->where('user_two',$userto)->get();
        $chekcon2=DB::table('conversations')->where('user_two',$userid)->where('user_one',$userto)->get();

        $allconversation=array_merge( $chekcon1->toArray(),$chekcon2->toArray());

        if(count($allconversation) != 0){

            $oldconversation = $allconversation[0]->id;

            $message=DB::table('messages')->insert([

                'user_from' => Auth::user()->id,
                'user_to' => $request->user_to,
                'message' => $request->message,
                'statut' =>1,
                'conversation_id' =>$oldconversation

            ]);
            return redirect()->route('home');
           
        }else
        {
                //new conversation

                $newconversation = DB::table('conversations')->insertGetId([

                    'user_one' => Auth::user()->id,
                    'user_two' => $userto,
                    'statut' => 1
    
                ]);
                $message=DB::table('messages')->insert([

                    'user_from' => Auth::user()->id,
                    'user_to' => $request->user_to,
                     'message' => $request->message,
                    'statut' =>1,
                     'conversation_id' =>$newconversation
    
                ]);
                return redirect()->route('home');
        }

        return redirect()->route('home');
     }

     public function sendmessage(Request $request){
             
            $conversation_id = $request->conversation_id;
            $message = $request->message;
            $fetchuserto1 = DB::table('messages')->where('conversation_id',$conversation_id)->where('user_from','!=',Auth::user()->id)->get();
            $fetchuserto2 = DB::table('messages')->where('conversation_id',$conversation_id)->where('user_to','!=',Auth::user()->id)->get();
            
   
            if( empty($fetchuserto1[0]->user_from) ){
                
                $userto =$fetchuserto2[0]->user_to ;
                
                $sendmessage = DB::table('messages')->insert([

                    'user_from' => Auth::user()->id,
                    'user_to' => $userto,
                    'message' => $message,
                    'statut' =>1,
                    'conversation_id' =>$conversation_id,
        
                  ]);
                  return redirect()->route('Messages.index');


            }
            elseif(empty($fetchuserto2[0]->user_to)){
     
             $userto = $fetchuserto1[0]->user_from;
             
             $sendmessage = DB::table('messages')->insert([

                'user_from' => Auth::user()->id,
                'user_to' => $userto,
                'message' => $message,
                'statut' =>1,
                'conversation_id' =>$conversation_id,
    
              ]);
              return redirect()->route('Messages.index');
            }
               
            
            
           
            

     }
}
