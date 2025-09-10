@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')
    <style>
        @media (min-width: 500px) {
            .progress-bar2 {
                display: none !important;
            }
        }
        p,ol{
            text-align: justify;
        }
        .red-label {
            color: black;
            font-weight: normal;
        }
    </style>
    <div class="container-fluid py-2">
        <div class="row min-vh-80 mt-5">
            <div class="col-lg-10 col-md-10 col-12 m-auto">
{{--                <h3 class="mt-3 mb-0 text-center">Add new Product</h3>--}}
{{--                <p class="lead font-weight-normal opacity-8 mb-7 text-center">This information will let us know more--}}
{{--                    about you.</p>--}}
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n5 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="multisteps-form__progress">
                                <button class="multisteps-form__progress-btn js-active " disabled>
                                    <span>1. Show Profile</span>
                                </button>
                                <button class="multisteps-form__progress-btn js-active" disabled>2. Application
                                    Form
                                </button>
                                <button class="multisteps-form__progress-btn js-active" disabled>3. Terms and
                                    Conditions
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>4. Review
                                </button>
                            </div>
                            <small class="progress-bar2 d-block text-center text-white">3. Terms and
                                Conditions</small>
                        </div>
                    </div>
                    <style>
                        .terms-box {
                            max-height: 300px; /* Adjust height as needed */
                            overflow-y: auto;
                        }
                    </style>
                    <form action="{{ route('terms.store') }}" method="POST">
                        @csrf
                    <div class="card-body">
                        <div class="form-check">
                            <div class="container">
                                <div class="row">
                                    <div class="col scr">
                                        <h5>Terms and Conditions</h5>
                                        <div class="terms-box border p-3 bg-light scrolling" style="overflow-y: scroll;">
                                            <div class="terms-content scrollable ">
                                                <h1>CONTRACT</h1>
                                                <p>In the event of any conflict, this Stipulation/Terms and Conditions will prevail over all other documents. All documents, including the Exhibitor Service Manual, can be found online at <a href="https://www.semiconindia.org">www.semiconindia.org</a>. This Contract is entered into between an exhibiting company <strong>("Exhibitor")</strong> and SEMI India <strong>(“SEMI”)</strong>.</p>

                                                <h2>PRIORITY POINTS, BOOTH SPACE ASSIGNMENT AND PAYMENT</h2>
                                                <p>Booth space assignment is based on a regional point system. Only SEMI Members can build and use points. Should the requested space be previously assigned or the floor layout change, SEMI will make reasonable efforts to provide suitable alternate space based on when application and payment were received. An Exhibitor's ultimate placement by SEMI shall be considered final, provided that SEMI reserves the rights to relocate an Exhibitor at any time, at its sole discretion, for the overall good of the show. SEMI is not obligated to reimburse the Exhibitor for any costs arising from such relocation.</p>

                                                <h2>PAYMENT TERMS</h2>
                                                <p><strong>100%</strong> payment (including 40% (non-refundable) deposit) must be submitted after the application is approved by SEMI. If an Exhibitor does not comply with these Payment Terms, SEMI reserves the right to release such Exhibitor's space for re-assignment, and shall be entitled to retain the 40% non-refundable deposit. Applicable prevailing India tax, (currently GST 18%) will be additional and due on 15 Aug 2025. Exhibiting companies will receive a tax invoice including GST on 1 August.</p>

                                                <h2>GENERAL RULES, TERMS AND CONDITIONS</h2>
                                                <ol>
                                                    <li>Each Exhibitor agrees that the rules and regulations of SEMI are made a part of this Contract and agrees to be bound by them. Each Exhibitor further agrees that SEMI has the full power to interpret and enforce all rules and regulations in the best interest of the SEMICON show.</li>
                                                    <li>The signatory to the Application for exhibit space or his designee shall be an official representative of the Exhibitor and shall have the authority to certify representatives and act on behalf of the Exhibitor in all negotiations.</li>
                                                    <li>Applications from companies with delinquent balances due to SEMI will not be processed. This includes, but is not limited to, unpaid cancellation fees from prior expositions.</li>
                                                    <li>SEMI Membership must be active at all times to receive member pricing; otherwise, SEMI will invoice for the non-member rate.</li>
                                                    <li>SEMI reserves the right to change the venue and date of the Exhibition under certain circumstances. In the event of a change of venue and/or date, or cancellation of the Exhibition, except as provided under the heading “Cancellation/Change of Exhibit,” the Exhibitors shall not be entitled to any claim for damages arising from such change or cancellation.</li>
                                                </ol>

                                                <h2>QUALIFICATIONS OF EXHIBITING COMPANY</h2>
                                                <p>Exhibitors must be manufacturers or independent representatives of manufacturers that produce equipment or materials for use by the semiconductor, flat panel display and electronic design automation industries, or that are used in relevant ancillary work (such as trade magazines or books, software houses, etc.). SEMI reserves the right to determine the eligibility of any product for display, and the Exhibitor shall immediately remove any product determined by SEMI to be ineligible for display upon SEMI's request. The Exhibitor represents and warrants that none of its products on display will infringe the rights of any third party. If any third party raises a claim against SEMI for the products displayed by the Exhibitor, SEMI shall have the right to terminate this Contract immediately upon notice to the Exhibitor and Exhibitor shall indemnify and hold SEMI harmless against all losses and liabilities associated with such a claim.</p>

                                                <h2>USE OF SPACE</h2>
                                                <p>An Exhibitor may not assign, sublet or re-sell, in whole or in part, their contracted space. An Exhibitor may share its contracted space with its affiliate for co-exhibiting, provided that:</p>
                                                <ol type="I">
                                                    <li>Such co-exhibitors shall comply with all conditions and rules and regulations applicable to the Exhibitor that is party to this Contract (a “primary Exhibitor”).</li>
                                                    <li>The primary Exhibitor shall continue to be primarily liable for all its obligations under this Contract.</li>
                                                    <li>The PRIMARY Exhibitor MUST have the prominent identification in its entire contracted booth space.</li>
                                                    <li>The contracted space MUST appear as one unified booth.</li>
                                                    <li>All booths MUST be staffed at all times during exhibit hours.</li>
                                                </ol>
                                                <h2>LIABILITY</h2>
                                                <p>TO THE FULLEST EXTENT PERMISSIBLE UNDER APPLICABLE LAW, IN NO EVENT SHALL SEMI BE RESPONSIBLE OR LIABLE FOR ANY SPECIAL, NON-COMPENSATORY, CONSEQUENTIAL, INDIRECT, INCIDENTAL, STATUTORY OR PUNITIVE DAMAGES OF ANY KIND, NOR SHALL SEMI BE RESPONSIBLE OR LIABLE FOR ECONOMIC LOSSES, INCLUDING, WITHOUT LIMITATION, GOODS, LOSS OF TECHNOLOGY, RIGHTS OR SERVICES, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGES, WHETHER UNDER CONTRACT THEORY, OR TORT INCLUDING NEGLIGENCE, TO THE EXTENT REASONABLE, STRICT LIABILITY OR OTHERWISE.</p>
                                                <p>Notwithstanding anything to the contrary in this Contract, the maximum aggregate liability of SEMI to any Exhibitor, affiliated Co-Exhibitors or any of their employees, officers, directors, representatives or subcontractors related to, or in connection with this Contract or the event, will be limited to the total amount of fees paid by the Exhibitor to SEMI hereunder in connection with the Event.</p>
                                                <p>To the fullest extent permissible under applicable law, SEMI will not be liable for damages to property or injuries to any persons from any cause whatsoever by reason of occupancy of exhibit space by Exhibitor, affiliated co-exhibitors, or any of their employees, subcontractors, agents or representatives.Further, each Exhibitor hereby agrees to indemnify, and holds harmless, SEMI and each of their respective officers, directors, employees, subcontractors, representatives and agents from all liabilities, losses, damages, costs, fees (including without limitation court costs and reasonable attorney’s fees) and expenses that might result from any cause whatsoever with respect to breach of this Contract; the acts, omissions or representations of Exhibitor and/or Co-Exhibitors; and/or the Exhibitor’s exhibit including, without limitation, theft or other loss from exhibit booth.The Exhibitor agrees to pay promptly for any and all damage to the exhibition building or its equipment, incurred through carelessness or otherwise, caused by the Exhibitor, affiliated Co-Exhibitors, or their employees, subcontractors, agents, or representatives.</p>
                                                <p>General security will be provided to Exhibitor, but SEMI shall in no event be liable for any loss or damages whatsoever due to any lack or failure of such security. Exhibitor assumes full responsibility for any loss of equipment and/or display material, resulting from theft or any other cause whatsoever.</p>
                                                <p>Notwithstanding the above, nothing in this Contract excludes or limits SEMI’s liability in relation to death or personal injury caused by (i) their respective negligence or willful or reckless misconduct; (ii) any fraud or fraudulent misrepresentation; and (iii) any other liability that cannot, as a matter of law, be limited or excluded.</p>

                                                <h2>TRADEMARKS AND OTHER INTELLECTUAL PROPERTY RIGHTS</h2>
                                                <p>Exhibitor agrees that any content or materials that include any SEMI trademark must be approved in writing by SEMI prior to publication. If approved, such use shall be in accordance with applicable trademark law and SEMI’s trademark guidelines as revised from time to time. Exhibitor agrees that any such use shall inure solely to the benefit of SEMI, and that Exhibitor shall not obtain any right in the SEMI trademarks beyond the rights expressly granted by SEMI. Exhibitor further agrees not to register any of SEMI’s trademark or confusingly similar trademarks with any governmental authority, and not to challenge the rights of SEMI in any SEMI trademark. Exhibitor agrees to modify or remove any content or material published by Exhibitor in connection with this provision upon SEMI’s request.</p>
                                                <p>Exhibitor represents and warrants that it owns or has a license to all rights, title and interest in and to all materials including its logos and trademarks, and any patented designs and inventions, copyrighted works, service marks, trade, business and domain names, and any other intellectual property that it provides to SEMI <strong>("Intellectual Property")</strong> and that the use of any such Intellectual Property does not violate any license agreement which Exhibitor may have with any third party or infringe on the rights of any third party.</p>
                                                <p>Exhibitor hereby grants to SEMI a non-exclusive, limited license to use Exhibitor’s trademarks and logos and other Intellectual Property from the date of acceptance by SEMI of Exhibitor’s application and until and including the period of the Event solely in connection with SEMI’s promotion of the Event and Exhibitor’s participation in the Event, including without limitation on the SEMICON India website and in publications, advertising, and brochures. Exhibitor must supply samples of such trademarks and logos and other Intellectual Property and agrees to indemnify, and hold harmless SEMI and each of their respective officers, directors, employees, subcontractors, representatives and agents from all liabilities, losses, damages, costs, fees (including without limitation attorney’s fees) and expenses that might result from use of such logos and trademarks and other Intellectual Property in connection with the Event, including as a result of any third-party claims against SEMI for infringing or misappropriating any intellectual property rights of the third party.</p>

                                                <h2>EVENT CANCELLATION / CHANGE OF EXHIBIT</h2>
                                                <p>If SEMI should be unable to hold the exhibition for any cause beyond its reasonable control, or if it cannot permit the Exhibitor to occupy its space due to causes beyond SEMI’s reasonable control,SEMI has the right to cancel the exhibit with no further liability than a refund of the stand space rental less a proportionate share of the exhibition expenses incurred by SEMI.SEMI shall in no event be liable for incidental or consequential damages to Exhibitor arising from or relating to such cancellation. Should Exhibitor's display and/or material fail to arrive, Exhibitor is nevertheless responsible for the rental of its exhibit space.</p>

                                                <h2>COMPLIANCE WITH RULES</h2>
                                                <p>Each Exhibitor assumes all responsibility for compliance with pertinent ordinances, regulations, and codes of duly authorized local, state, federal, and international government bodies concerning fire, safety, and health, together with the rules and regulations contained in the Exhibitor Services Manual. All aisles and service areas must be kept clear with boundaries set by the Fire Department and SEMI.</p>

                                                <h2>INSURANCE</h2>
                                                <p>Exhibitor, at its sole cost and expense, will insure its activities and equipment used in connection with the Event and will obtain, keep in force, and maintain a valid commercial and civil insurance policy (contractual liability included) and errors and omissions in each case in an amount equivalent to US$1,000,000. If the above insurance is written on a claims-made form, it will continue for two (2) years following the Event. Such coverage and limits will not in any way limit the liability of Exhibitor. If requested, Exhibitor must furnish SEMI with certificates of insurance evidencing compliance with all requirements, and Exhibitor will promptly notify SEMI of any material modification of the insurance policies. Such certificates will provide for thirty (30) days' advance written notice to SEMI of any cancellation of insurance policies; indicate that SEMI has been endorsed as an additional insured under such coverage; and include a provision that the coverage will be primary and will not participate with, nor will be excess over, any valid and collectable insurance or program of self-insurance maintained by SEMI.</p>

                                                <h2>CANCELLATION OR REDUCTION OF EXHIBIT SPACE BY EXHIBITING COMPANY</h2>
                                                <ol>
                                                    <li>In the event of cancellation (partial or full) a written notice must be received by SEMI.</li>
                                                    <li>If canceled <strong>on or before April 30, 2025</strong>, a <strong>cancellation fee of 40%</strong> of the canceled space will be assessed by SEMI.</li>
                                                    <li>If canceled <strong>after April 30, 2025</strong>, a <strong>cancellation fee of 100%</strong> of the canceled space will be assessed by SEMI.</li>
                                                    <li>SEMI will issue the final invoice reflecting all fees imposed on Exhibitor's account per the terms and conditions of this Contract.</li>
                                                </ol>
                                                <p><strong>Cancellation fee assessments are not transferable and may not be used for any other payments due.</strong></p>
                                                <p>Reduction of exhibit space may result in booth relocation. SEMI reserves the right to reassign cancelled booth space, regardless of the liquidated damage assessment. Subsequent reassignment of cancelled space does not relieve the canceling Exhibitor of the obligation to pay the assessment. SEMI must receive written notification of any cancellation. All booths must be set and show ready by 6:00 pm on the day prior to the opening of the event. Failure to do so will be considered a cancellation unless SEMI has been notified and has approved in advance otherwise.</p>

                                                <h2>DATA PROTECTION</h2>
                                                <p>SEMI may collect and process personal company data in order to perform its obligations pursuant to this Contract as well as to provide Exhibitors information about future events. Such data will not be transferred or shared with any other entity other than SEMI and its affiliates. By submitting personal company data to SEMI, Exhibitors expressly consent, on behalf of their officers and employees, to the transfer and processing of that personal company data in India and in the United States. Exhibitors have the right to access and correct their personal company data, and in some circumstances may be entitled to delete their personal company data, by contacting SEMI show management.</p>


                                                <h2>GOVERNING LAW / ARBITRATION</h2>
                                                <p>This agreement shall be governed by the laws of India without regard to principles of conflicts of laws. Any controversy, dispute, or claim arising out of or relating to this agreement, including the existence, validity, interpretation, performance, breach or termination hereof, or any other dispute between the Exhibitor and SEMI arising out of or relating to the SEMICON Show, shall be referred to and finally resolved by arbitration administered by the Bangalore International Mediation Arbitration and Conciliation Centre (BIMACC) under the BIMACC Rules of Arbitration in force when the Notice of Arbitration is submitted. The place of arbitration shall be in Bangalore. The number of arbitrators shall be one. The arbitration proceedings shall be conducted in English. The arbitral award shall be final and binding upon both parties.In the event of any conflict between these governing law/arbitration rules and the provisions of these terms and conditions, the provisions of these terms and conditions shall govern.</p>
                                                <p>Notwithstanding the foregoing, should adequate grounds exist for seeking immediate injunctive relief for a violation of any term or condition of this agreement, any party hereto may seek and obtain such relief, provided that, upon obtaining such relief, such action shall be stayed pending the resolution of arbitration proceedings.</p>
                                                <p>Judgment upon the decision rendered or awarded by the arbitrator may be entered in any court having jurisdiction thereof, or application may be made to such court for a judicial recognition of the decision or award or an order of enforcement thereof, as the case may be. The costs of arbitration including, inter alia, reasonable attorneys’ fees, expenses associated with the arbitration, and the costs of filing or enforcing the arbitration, all as determined by the arbitrator, shall be paid entirely by the non-prevailing party.</p>

                                                <h2>GENERAL</h2>
                                                <p>The relationship between the parties is that of independent contractors. Exhibitor is not an employee, agent, partner or legal representative of SEMI and shall have no authority to assume or create obligations on behalf of SEMI or the Event.Exhibitor shall not assign to a third party its rights, or obligations, or any portion thereof without the prior written consent of SEMI, which consent SEMI may grant or withhold at its sole discretion. Any attempted assignment without such consent by SEMI shall be void and of no effect and shall constitute a material breach by Exhibitor of its obligations hereunder. SEMI shall have the right to subcontract to a third party its rights, or obligations, or any portion thereof.This Contract is the only agreement between the parties pertaining to its subject matter, and supersedes any draft or prior or contemporaneous agreement, discussion, or representation (whether written or oral) between the parties. All conditions, warranties or other terms not expressly contained in this Contract which might have effect between the parties or be implied or incorporated into this Contract, whether by statute, common law or otherwise, are hereby excluded. This agreement may be amended or modified in a writing signed by each party.Aside from the parties hereto, no other person or entity is entitled to benefit from the rights and obligations hereunder and there are no third-party beneficiaries hereto.</p>
                                                <p>The failure of either party to assert a right hereunder or to insist upon compliance with any term or condition of this agreement will not constitute a waiver of that right or excuse a similar subsequent failure to perform any such term or condition by the other party. None of the terms and conditions of this Contract can be waived except by the written consent of the Party waiving compliance.If any provision of this agreement is held to be invalid or unenforceable by a court of competent jurisdiction, then the remaining provisions will nevertheless remain in full force and effect, and the parties shall negotiate in good faith a substitute, valid, and enforceable provision that most nearly reflects the parties’ intent in entering into this Contract.</p>
                                                <div class="p-3 mb-3 " style=" border: 2px solid black; text-align: center">
                                                    <strong>It is each exhibiting company’s responsibility to read and comply with all rules and regulations as stated in the Exhibitor Services Manual. Each exhibiting company will be fully responsible for all costs involved should the exhibiting company violate rules and regulations that require remedial action by SEMI.</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            .terms-box {
                                max-height: 300px; /* Adjust height as needed */
                                overflow-y: auto;
                            }
                        </style>



                    </div>


                    @php
                        $isDisabled = (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved' )  ) ? 'disabled' : '';
                         $checked = (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved' )) ? 'checked' : '';
                    @endphp

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms_accepted" style="margin-left: 40px;"
                               name="terms_accepted" value="1" required {{ $checked }} {{$isDisabled}}>
                        <label class="form-check-label red-label" for="terms_accepted">
                            I have read and accept the above terms and conditions.
                        </label>
                    </div>
                    <div class="row mt-3 justify-content-center align-content-center">
                        <div class="col-12 col-sm-4 mt-3 mt-sm-0 text-center">
                            @if (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved' ) )
                                <a href="{{ route('application.preview') }}" class="btn btn-info ms-auto mb-3 js-btn-next">Next</a>
                            @else
                                <button class="btn btn-info ms-auto mb-3 js-btn-next" type="submit" {{ $isDisabled }}>Submit</button>
                            @endif
                        </div>
                    </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
