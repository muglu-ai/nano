<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ExhibitionParticipant;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CoExhibitor;



class DocumentsContoller extends Controller
{

    // //construct function to check if user is logged in
    public function __construct()
    {
        if (auth()->check() && auth()->user()->role !== 'exhibitor') {
            return redirect('/login');
        }
    }

    //get the logged in userId and return the  applicationId, userId

    public function getUserApplicationInfo()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect('/login');
        }

        $applicationId = Application::where('user_id', $user->id)->value('id', 'role');
        $application_id = Application::where('user_id', $user->id)->value('application_id');

        return response()->json([
            'userId' => $user->id,
            'role' => $user->role,
            'applicationId' => $applicationId,
            'application_id' => $application_id,
        ]);
    }


    public function transport_letter()
    {
        $id = $this->getUserApplicationInfo()->getData(true);
        $data = [];
        $applicationId = null;

        if ($id['role'] == 'co-exhibitor') {
            $coexhibitor = CoExhibitor::where('user_id', $id['userId'])->firstOrFail();
            
            //curate the application data from coexhibitor
            $data = [
                'type' => 'Co-Exhibitor',
                'contactPerson' => trim($coexhibitor->contact_person),
                'companyName' => $coexhibitor->co_exhibitor_name,
                'Address' => $coexhibitor->address1,
                'City' => $coexhibitor->city,
                'State' => $coexhibitor->state,
                'Pincode' => $coexhibitor->zip,
                'Country' => $coexhibitor->country,
                'date' => $coexhibitor->approved_At,
                'boothNumber' => $coexhibitor->booth_number,
                'application_id' => $coexhibitor->co_exhibitor_id
            ];
            
            $applicationId = $coexhibitor->co_exhibitor_id;
        } else {
            $application = Application::where('user_id', $id['userId'])->first();

            if (!$application) {
                return redirect()->route('event.list')->with('error', 'Application not found.');
            }

            $eventContact = $application->eventContact;
            if (!$eventContact) {
                return redirect()->route('event.list')->with('error', 'Event contact not found.');
            }

            $contactPerson = trim(collect([
                optional($eventContact)->salutation,
                optional($eventContact)->first_name,
                optional($eventContact)->last_name
            ])->filter()->implode(' '));

            $data = [
                'type' => 'Exhibitor',
                'contactPerson' => $contactPerson,
                'companyName' => $application->company_name,
                'Address' => $application->address,
                'City' => $application->city_id,
                'State' => $application->state->name ?? '',
                'Pincode' => $application->postal_code,
                'Country' => $application->country->name ?? '',
                'date' => $application->approved_date,
                'boothNumber' => $application->stallNumber,
                'application_id' => $application->application_id
            ];
            
            $applicationId = $application->application_id;
        }

        // Check if PDF already exists
        $filePath = public_path('storage/transport_letter/' . $applicationId . '_transport_letter.pdf');
        
        if (!file_exists($filePath)) {
            // Generate new PDF
            $pdf = PDF::loadView('documents.transport_letter', compact('data'))->setPaper('a3');
            $pdf->save($filePath);
        }

        // Convert to relative path for view
        $filePath = str_replace(public_path(), '', $filePath);

        // Return view with PDF path and application data
        return view('documents.documentsView', [
            'pdfPath' => $filePath,
            'application' => $id['role'] == 'co-exhibitor' ? null : $application
        ]);
    }
    public function invitation()
    {
        $id = $this->getUserApplicationInfo()->getData(true);
        $data = [];
        $applicationId = null;

        if ($id['role'] == 'co-exhibitor') {
            $coexhibitor = CoExhibitor::where('user_id', $id['userId'])->firstOrFail();
            
            //curate the application data from coexhibitor
            $data = [
                'type' => 'Co-Exhibitor',
                'contactPerson' => trim($coexhibitor->contact_person),
                'companyName' => $coexhibitor->co_exhibitor_name,
                'Address' => $coexhibitor->address1,
                'City' => $coexhibitor->city,
                'State' => $coexhibitor->state,
                'Pincode' => $coexhibitor->zip,
                'Country' => $coexhibitor->country,
                'date' => $coexhibitor->approved_At,
                'boothNumber' => $coexhibitor->booth_number,
                'application_id' => $coexhibitor->co_exhibitor_id
            ];
            
            $applicationId = $coexhibitor->co_exhibitor_id;
        } else {
            $application = Application::where('user_id', $id['userId'])->first();

            if (!$application) {
                return redirect()->route('event.list')->with('error', 'Application not found.');
            }

            $eventContact = $application->eventContact;
            if (!$eventContact) {
                return redirect()->route('event.list')->with('error', 'Event contact not found.');
            }

            $contactPerson = trim(collect([
                optional($eventContact)->salutation,
                optional($eventContact)->first_name,
                optional($eventContact)->last_name
            ])->filter()->implode(' '));

            $data = [
                'type' => 'Exhibitor',
                'contactPerson' => $contactPerson,
                'companyName' => $application->company_name,
                'Address' => $application->address,
                'City' => $application->city_id,
                'State' => $application->state->name ?? '',
                'Pincode' => $application->postal_code,
                'Country' => $application->country->name ?? '',
                'date' => $application->approved_date,
                'boothNumber' => $application->stallNumber,
                'application_id' => $application->application_id
            ];
            
            $applicationId = $application->application_id;
        }


       // dd($data);

        // Check if PDF already exists
        $filePath = public_path('storage/invitation_letters/' . $applicationId . '_invitation_letter.pdf');
        
        if (!file_exists($filePath)) {
            // Generate new PDF
            $pdf = PDF::loadView('documents.new_invitation_letter', compact('data'))->setPaper('a3');
            $pdf->save($filePath);
        }

        // Convert to relative path for view
        $filePath = str_replace(public_path(), '', $filePath);

        // Return view with PDF path and application data
        return view('documents.documentsView', [
            'pdfPath' => $filePath,
            'application' => $id['role'] == 'co-exhibitor' ? null : $application
        ]);
    }

    // display exhibitor_manual
    public function exhibitor_manual()
    {
        //path         public_path/assets/docs/Exhibitior-Manual-SEMICON-2025.pdf

        $filePath = null;
       //dd($filePath);
        //return view with the file path
        if (file_exists($filePath)) {
            $filePath = str_replace(public_path(), '', $filePath);

            // Convert to full URL using APP\_URL
            $filePath = rtrim(config('constants.APP_URL'), '/') . $filePath;


            // dd($filePath);
            return view('documents.documentsView', ['pdfPath' => $filePath]);
        } else {
            //print that it is coming soon
            $$filePath = null;
            return view('documents.documentsView', ['pdfPath' => $filePath]);
            return redirect()->route('event.list')->with('error', 'Exhibitor manual not found.');
        }
    }

    //exhibitor guide 
    public function exhibitor_guide()
    {
        //path         public_path/assets/docs/Exhibitor-Guide-SEMICON-2025.pdf

        $filePath = public_path('assets/docs/SEMICON-Exhibitor-Portal-Guide.pdf');
        //return view with the file path
        if (file_exists($filePath)) {
            $filePath = str_replace(public_path(), '', $filePath);

            // dd($filePath);
            return view('documents.documentsView', ['pdfPath' => $filePath]);
        } else {
            return redirect()->route('event.list')->with('error', 'Exhibitor guide not found.');
        }
    }
    public function faqs()
    {
        //path         public_path/assets/docs/Exhibitor-Guide-SEMICON-2025.pdf

        $filePath = public_path('assets/docs/SEMICON-India-Exhibitor-&-Visitor-FAQs.pdf');

        //return view with the file path
        if (file_exists($filePath)) {
            $filePath = str_replace(public_path(), '', $filePath);

            // dd($filePath);
            return view('documents.documentsView', ['pdfPath' => $filePath]);
        } else {
            return redirect()->route('event.list')->with('error', 'FAQs not found.');
        }
    }
}
