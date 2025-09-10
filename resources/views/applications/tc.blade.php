<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="https://www.semiconindia.org/themes/custom/semicon/favicon.ico" type="image/vnd.microsoft.icon" />
    <title>
        @yield('title' , 'SEMICON India 2025 Terms and Conditions')
    </title>



    <link rel="stylesheet" href="/assets/css/form_custom.css">
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="/asset/css/nucleo-icons.css" rel="stylesheet" />
    <link href="/asset/css//nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="/asset/css/material-dashboard.min.css?v=3.1.0" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>
    <style>
        @media (min-width: 500px) {
            .progress-bar {
                display: none !important;
            }
        }

        /* Loader Style */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

<div id="loader"  class="loader">
    <div class="spinner"> </div>
</div>

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

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg " id="main-content" style="display: none;">
    <div class="logo-container text-start" style="text-align: center; padding: 20px;">
        <a class="navbar-brand ms-2" href="/terms-conditions">
            <svg class="navbar-brand-img" width="100" height="50" viewBox="0 0 163 40" xmlns="http://www.w3.org/2000/svg">
                <path d="M43.751 18.973c-2.003-.363-4.369-.454-7.009-.363-8.011 9.623-20.846 17.974-29.403 19.064-2.093.272-3.641.091-4.915-.454.819.726 2.184 1.362 4.096 1.725 8.193 1.634 23.213-1.544 33.499-7.081 10.286-5.538 12.016-11.348 3.732-12.891zm-31.587 2.996c8.557-5.175 19.662-8.897 29.129-10.077C45.299 4.357 43.387-.454 35.923.545c-9.012 1.18-22.758 10.439-30.586 20.607-5.735 7.444-6.737 13.254-3.46 15.523-2.366-3.54 1.275-9.169 10.287-14.706zm58.35-.726l-4.643-1.271c-1.274-.363-1.911-.908-1.911-1.634 0-1.271 2.184-1.907 4.278-1.907 1.912 0 3.186.636 4.187 1.09.638.272 1.184.544 1.73.544 1.457 0 1.73-.635 1.73-1.18l-.182-.635c-.82-1.09-4.37-1.998-8.102-1.998-3.641 0-7.373 1.635-7.373 4.267 0 2.27 2.184 3.177 4.096 3.722l5.28 1.453c1.547.454 3.004.907 3.004 2.178 0 1.18-1.639 2.361-4.734 2.361-2.458 0-4.005-.817-5.098-1.453-.728-.363-1.274-.726-1.82-.726-.82 0-1.639.726-1.639 1.271 0 1.271 3.55 3.086 8.466 3.086 5.189 0 8.648-1.906 8.648-4.629-.091-2.724-3.004-3.722-5.917-4.539zm22.757-6.991c-6.554 0-10.013 4.086-10.013 8.08 0 3.722 2.731 8.079 10.559 8.079 5.371 0 9.103-2.178 9.103-3.268 0-1.271-1.183-1.271-1.638-1.271-.546 0-1.092.273-1.73.727-1.183.726-2.822 1.634-5.917 1.634-3.823 0-6.281-2.361-6.554-4.721h13.928c1.547 0 2.276-.454 2.276-1.452-.091-3.813-3.187-7.808-10.014-7.808zm6.19 6.991h-12.38c.273-2.452 2.367-4.812 6.19-4.812 3.732 0 5.917 2.451 6.19 4.812zm53.253-6.991c-1.093 0-1.73.545-1.73 1.544v12.981c0 .999.637 1.544 1.73 1.544 1.092 0 1.729-.545 1.729-1.544V15.796c0-.999-.637-1.544-1.729-1.544zm-26.399 2.633c1.457-1.543 4.096-2.633 6.645-2.633 4.006 0 8.375 1.816 8.375 5.72v8.896c0 .999-.637 1.543-1.73 1.543-1.092 0-1.729-.544-1.729-1.543v-8.442c0-2.542-1.639-3.722-4.916-3.722-2.458 0-5.006 1.361-5.006 3.722v8.442c0 .999-.638 1.543-1.73 1.543s-1.73-.544-1.73-1.543v-8.442c0-2.452-2.639-3.813-5.006-3.813-3.368 0-4.916 1.271-4.916 3.813v8.442c0 .999-.637 1.543-1.729 1.543-1.093 0-1.73-.544-1.73-1.543v-8.896c0-3.904 4.37-5.72 8.375-5.72 2.64 0 5.189.999 6.645 2.633l.182.091v-.091zm33.044-1.906h-.455a.196.196 0 0 1-.182-.182c0-.091.091-.181.182-.181h1.365c.091 0 .182.09.182.181a.196.196 0 0 1-.182.182h-.455v1.634c0 .091-.091.181-.182.181-.182 0-.182-.09-.182-.181v-1.634h-.091zm1.365 0c0-.273.091-.363.273-.363.091 0 .273 0 .364.181l.547 1.362.455-1.362c.091-.181.182-.181.364-.181s.273.09.273.363v1.634c0 .091-.091.181-.182.181s-.182-.09-.182-.181V15.07l-.546 1.543c0 .181-.091.181-.182.181s-.182-.09-.182-.181l-.547-1.543v1.543c0 .091-.091.181-.182.181s-.182-.09-.182-.181v-1.634h-.091z" id="Shape" fill-rule="nonzero"></path>
            </svg>
            <div class="">
            <span class="navbar-brand-text">SEMICON India 2025</span>
            </div>
        </a>
        <div class="text-center">
            <h1 class="text-black-50 text-center text-lg">Terms and Conditions</h1>
        </div>
    </div>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-lg-12 col-md-10 col-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="form-check">
                            <div class="container">
                                <div class="row">
                                    <div class="col scr">

                                        <div class="terms-box border p-3 bg-light">
                                            <div class="terms-content ">
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
                    </div>
                </div>
            </div>

        </div>
    </div>
    <footer class="footer py-3 w-100 mt-3">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-12 col-md-4 text-md-start d-flex justify-content-center justify-content-md-start align-items-center">
                    <p class="mb-0 text-wrap text-sm text-white">© Copyright <span id="currentYear"></span> SEMICON India. All Rights Reserved.</p>
                </div>

                <!-- Black Vertical Separator -->
                <div class="separator d-none d-md-block"></div>

                <div class="col-12 col-md-3 text-center d-flex justify-content-center align-items-center">
                    <a href="https://portal.semiconindia.org/terms-conditions" class="nav-link text-white">Terms & Conditions</a>
                </div>

                <!-- Black Vertical Separator -->
                <div class="separator d-none d-md-block"></div>

                <div class="col-12 col-md-4 text-md-end d-flex justify-content-center justify-content-md-end align-items-center">
                    <p class="mb-0 text-wrap text-sm text-white">Powered by MM Activ Sci-Tech Communications PVT. LTD.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById("currentYear").textContent = new Date().getFullYear();
    </script>

    <style>
        .footer {
            background-color: #3f504e; /* Dark background for a strong footer band */
            padding: 20px 0;
            border-top: 3px solid #ffffff20; /* Light border for a sleek look */
            box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.2); /* Soft shadow effect */
        }

        .separator {
            width: 2px !important;  /* Forces exact width */
            height: 25px !important; /* Forces exact height */
            background-color: #FFFFFF;
            margin: 0 10px !important; /* Ensures no extra spacing */
            padding: 0 !important; /* Removes any internal padding */
            display: inline-block; /* Prevents extra spacing issues */
        }

        .text-sm {
            font-size: 14px;
        }

        .nav-link {
            color: #ffffff !important;
            font-weight: 500;
        }

        .nav-link:hover {
            text-decoration: underline;
        }
    </style>
</main>



<script>
    window.onload = function() {
        // Hide the loader and show the content
        document.getElementById('loader').style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
    };
</script>

<script>
    $(window).on('load', function() {
        // Hide the loader and show the content
        $('#loader').fadeOut();
        $('#main-content').fadeIn();
    });
</script>
