<x-app-layout>

    <x-slot name="header">

        <div class="d-flex justify-content-between align-items-center">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Cypress Dashboard') }}
            </h2>

        </div>

    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                        <div class="p-6">

                            <h4>
                                Cypress Test Cases
                            </h4>

                            <p class="text-gray-600">
                                Create, edit, clone, delete and export
                                automated test cases.
                            </p>

                            <a
                                href="{{ route('test-cases.index') }}"
                                class="btn btn-primary"
                            >
                                Manage Test Cases
                            </a>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                        <div class="p-6">

                            <h4>
                                Test Run History
                            </h4>

                            <p class="text-gray-600">
                                Search and filter Cypress execution history.
                            </p>

                            <a
                                href="{{ route('test-runs.index') }}"
                                class="btn btn-dark"
                            >
                                View Test Runs
                            </a>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                        <div class="p-6">

                            <h4>
                                Cypress Analytics
                            </h4>

                            <p class="text-gray-600">
                                View pass rate, failures and duration analytics.
                            </p>

                            <a
                                href="{{ route('test-runs.dashboard') }}"
                                class="btn btn-success"
                            >
                                Open Analytics
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>