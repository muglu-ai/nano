
<div class="fixed-sidebar-form">
    <header class="mn-header navbar-fixed">
        <nav class="cyan darken-1">
            <div class="nav-wrapper row" style="display: flex; align-items: center; justify-content: space-between;">
                <div class="header-title col s3 m3" style="display: flex; align-items: center; gap: 15px;">
                    <!-- Logo & Title Container -->
                    <span class="chapter-title" style="display: flex; align-items: center; gap: 10px;">
        <svg class="navbar-brand-img" width="70" height="50" viewBox="0 0 163 40" xmlns="http://www.w3.org/2000/svg">
            <path d="M43.751 18.973c-2.003-.363-4.369-.454-7.009-.363-8.011 9.623-20.846 17.974-29.403 19.064-2.093.272-3.641.091-4.915-.454.819.726 2.184 1.362 4.096 1.725 8.193 1.634 23.213-1.544 33.499-7.081 10.286-5.538 12.016-11.348 3.732-12.891zm-31.587 2.996c8.557-5.175 19.662-8.897 29.129-10.077C45.299 4.357 43.387-.454 35.923.545c-9.012 1.18-22.758 10.439-30.586 20.607-5.735 7.444-6.737 13.254-3.46 15.523-2.366-3.54 1.275-9.169 10.287-14.706zm58.35-.726l-4.643-1.271c-1.274-.363-1.911-.908-1.911-1.634 0-1.271 2.184-1.907 4.278-1.907 1.912 0 3.186.636 4.187 1.09.638.272 1.184.544 1.73.544 1.457 0 1.73-.635 1.73-1.18l-.182-.635c-.82-1.09-4.37-1.998-8.102-1.998-3.641 0-7.373 1.635-7.373 4.267 0 2.27 2.184 3.177 4.096 3.722l5.28 1.453c1.547.454 3.004.907 3.004 2.178 0 1.18-1.639 2.361-4.734 2.361-2.458 0-4.005-.817-5.098-1.453-.728-.363-1.274-.726-1.82-.726-.82 0-1.639.726-1.639 1.271 0 1.271 3.55 3.086 8.466 3.086 5.189 0 8.648-1.906 8.648-4.629-.091-2.724-3.004-3.722-5.917-4.539z"></path>
        </svg>
        <span style="font-weight: bold; font-size: 16px;">SEMICON</span>
    </span>

                    <!-- Onboarding and Sponsorship Buttons -->
                    <div style="display: flex; gap: 10px;">
                        <a href="" class="waves-effect waves-light btn-small">
                            Onboarding
                        </a>
                        <a href="" class="waves-effect waves-light btn-small">
                            Sponsorship
                        </a>
                    </div>
                </div>


                <!-- Sign Out Button -->
                <div class="col s9 m9 right-align">
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="waves-effect waves-grey" style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">
                            <i class="material-icons">exit_to_app</i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

</div>


