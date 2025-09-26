<?php


namespace App\Http\Controllers;

use App\Models\ExhibitorInfo;
use App\Models\ExhibitorProduct;
use App\Models\ExhibitorPressRelease;
use App\Models\Application;
use http\Env\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SponsorInvoiceMail;

class ExhibitorInfoController extends Controller
{

    //construct function 
    /*public function __construct()
    {
        // $user = auth()->user();
        // if (!auth()->check()) {
        //     redirect('/login')->send();
        // }
        // if ($user && $user->role == 'exhibitor') {
        //     $application = Application::where('user_id', $user->id)
        //         ->where('submission_status', 'approved')
        //         ->whereHas('invoices.payments', function ($query) {
        //             $query->where('status', 'successful');
        //         })
        //         ->first();

        //         dd($application);

        //     if (!$application) {
        //         redirect()->route('event.list')->send();
        //     }
        // }
    }
*/

// get the application id from application table where user_id is logged in user id
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // check if the user is exhibitor
            if (Auth::user()->role != 'exhibitor') {
                return redirect()->route('event.list')->with('error', 'You are not authorized to access this page.');
            }

            $applicationId = Application::where('user_id', Auth::id())
                ->where('submission_status', 'approved')
                ->whereHas('invoices.payments', function ($query) {
                    $query->where('status', 'successful');
                })
                ->value('id');

            if (!$applicationId) {
                return redirect()->route('event.list')->with('error', 'You are not authorized to access this page.');
            }

            return $next($request);
        });
    }

    //function application id
    public function getApplicationId()
    {
        return Application::where('user_id', Auth::id())
            ->where('submission_status', 'approved')
            ->whereHas('invoices.payments', function ($query) {
                $query->where('status', 'successful');
            })
            ->value('id');
    }


    public function showForm(Request $request)
    {

        // dd($request->all());

        //check user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // // check if the user is exhibitor
        // if (Auth::user()->role != 'exhibitor') {
        //     return redirect()->route('event.list')->with('error', 'You are not authorized to access this page.');
        // }

       // get the application id from application table where user_id is logged in user id
        $applicationId = Application::where('user_id', Auth::id())
            ->where('submission_status', 'approved')
            ->whereHas('invoices.payments', function ($query) {
                $query->where('status', 'successful');
            })
            ->value('id');

            // dd($applicationId);





        // check if the user is 
        $application = Application::findOrFail($applicationId);
        $add1 = $application->address;
        $city = $application->city_id;
        $state = $application->state_id->name;
        $country = $application->country_id->name;
        $zip = $application->zip;
        $application->full_address = $add1 . ', ' . $city . ', ' . $state . ', ' . $country . ', ' . $zip;

        dd($application->full_address);




        $slug = "Exhibitor Directory Information";

        //find the exhibitor info from exhibitor_info table where application_id is application id
        $exhibitorInfo = ExhibitorInfo::where('application_id', $applicationId)->first();
        return view('exhibitor_info.form', compact('application', 'slug', 'exhibitorInfo'));
    }

    public function storeExhibitor(Request $request)
    {
          $applicationId = $this->getApplicationId();
          // pass this application id to the request
        $request->merge(['application_id' => $applicationId]);

        // dd($request->all());
        $data = $request->validate([
            'application_id' => 'required|integer |exists:applications,id',
            'fascia_name' => 'required|string|max:255',
            'salutation' => 'required|string|max:15',
            'contact_first_name' => 'required |string|max:255',
            'contact_last_name' => 'required |string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:16',
            'description' => 'required|string|max:1000',
            'logo' => 'nullable|image|max:2048',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
            'facebook' => 'nullable|url',
            'youtube' => 'nullable|url',
        ]);

        // get the application id from application table where user_id is logged in user id


        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // create or update if already exists give the individual column names 
        
        $exhibitor = ExhibitorInfo::updateOrCreate(
            ['application_id' => $applicationId],
            [
            'fascia_name' => $data['fascia_name'],
            'contact_person' => $data['salutation'] . ' ' . $data['contact_first_name'] . ' ' . $data['contact_last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'description' => $data['description'],
            'logo' => $data['logo'] ?? (ExhibitorInfo::where('application_id', $applicationId)->value('logo')),
            'linkedin' => $data['linkedin'] ?? null,
            'instagram' => $data['instagram'] ?? null,
            'facebook' => $data['facebook'] ?? null,
            'youtube' => $data['youtube'] ?? null,
            'application_id' => $data['application_id'],
            ]
        );


        // $exhibitor = ExhibitorInfo::create($data);

        return redirect()->route('exhibitor.products', $exhibitor->id);
    }

    public function showProductForm(Request $request)
    {
        $slug = "Exhibitor Product Information";
        $applicationId = $this->getApplicationId();
        // check if the user is exhibitor
        if (Auth::user()->role != 'exhibitor') {
            return redirect()->route('event.list')->with('error', 'You are not authorized to access this page.');
        }

        // find the exhibitor info from exhibitor_info table where application_id is application id
        $exhibitorInfo = ExhibitorInfo::where('application_id', $applicationId)->first();
        // check if the exhibitor info is there or not
        if (!$exhibitorInfo) {
            return redirect()->route('exhibitor.form')->with('error', 'Please fill the exhibitor information form first.');
        }


        // check if the exhibitorProduct is there or not
        $exhibitorProducts = ExhibitorProduct::where('application_id', $applicationId);

        // print_r($applicationId);
        // dd($exhibitorProducts);

        // dd($exhibitorProducts->count());
        if ($exhibitorProducts->count() == 1) {
            $exhibitorProducts = $exhibitorProducts->get();
        } else {
            $exhibitorProducts = $exhibitorProducts->get();
        }




        // $exhibitor = ExhibitorInfo::findOrFail($id);
        return view('exhibitor_info.product_form', compact('exhibitorInfo', 'slug', 'exhibitorProducts'));   
    }

    public function productStore(Request $request)
    {
        $applicationId = $this->getApplicationId();
        

        $data = $request->validate([
            'product_name' => 'required',
            'description' => 'required|string|max:1000',
            'product_image' => 'required|image|max:2048',
        ]);



        if ($request->hasFile('product_image')) {
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        // create a new product using application id
      
        $exhibitorInfo = ExhibitorInfo::where('application_id', $applicationId)->first();
        //create a new product using application id
        $data['application_id'] = $applicationId;
        $product = ExhibitorProduct::create([
            'application_id' => $applicationId,
            'product_name' => $data['product_name'],
            'description' => $data['description'],
            'product_image' => $data['product_image'] ?? null,
        ]);




        // $data['exhibitor_id'] = $id;
        // ExhibitorProduct::create($data);

        return back()->with('success', 'Product added.');
    }

    public function showPressForm($id)
    {
        $exhibitor = ExhibitorInfo::findOrFail($id);
        return view('exhibitor.press-form', compact('exhibitor'));
    }

    public function storePress(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required',
            'summary' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('press', 'public');
        }

        $data['exhibitor_id'] = $id;
        ExhibitorPressRelease::create($data);

        return back()->with('success', 'Press release uploaded.');
    }
}
