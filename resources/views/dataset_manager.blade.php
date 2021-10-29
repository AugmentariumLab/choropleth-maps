<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Datasets Manager</title>
    <link rel="stylesheet" href="/css/app.css" />
    <link rel="stylesheet" href="/css/dataset_manager.css" />
    <script src="/js/DatasetManager.js" async defer></script>
</head>

<body>
    @include('navbar')

    <div class="datasets-container" id="add-dataset-container">
        <h3 class="title is-3">Add a New Dataset</h3>
        <form id="add-dataset-form" action="/api/dataset" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="add-step">
                <h4 class="title is-4 has-text-centered">Step 1</h4>
                <h5 class="subtitle is-5 has-text-centered">Select a file</h5>
                <div class="file has-name is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="dataset-file" id="add-dataset-input-file" accept=".csv" required>
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                                Choose a file…
                            </span>
                        </span>
                        <span class="file-name" id="add-dataset-filename-span">
                            Filename
                        </span>
                    </label>
                </div>
            </div>
            <div class="add-step">
                <h4 class="title is-4 has-text-centered">Step 2</h4>
                <h5 class="subtitle is-5 has-text-centered">Add Metadata</h5>
                <div class="field">
                    <label class="label">Dataset Name</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="Hotels" id="add-dataset-name-input" name="dataset-name" , required>
                    </div>
                </div>
                <div class="control">
                    <label class="label">Latitude Column</label>
                    <div class="select">
                        <select id="add-dataset-latitude-select" name="latitude-column" required>
                        </select>
                    </div>
                </div>
                <div class="control">
                    <label class="label">Longitude Column</label>
                    <div class="select">
                        <select id="add-dataset-longitude-select" name="longitude-column" required>
                        </select>
                    </div>
                </div>
                <div class="control">
                    <label class="label">Zipcode Column</label>
                    <div class="select" required>
                        <select id="add-dataset-zip-select" name="zipcode-column">
                        </select>
                    </div>
                </div>
            </div>
            <div class="add-step">
                <h4 class="title is-4 has-text-centered">Step 3</h4>
                <h5 class="subtitle is-5 has-text-centered">Upload the dataset</h5>
                <div class="has-text-centered">
                    <button class="button is-link" id="add-dataset-upload-button">Upload</button>
                </div>
            </div>
        </form>
    </div>
    <div class="datasets-container" id="existing-datasets-container">
        <h3 class="title">Existing Datasets</h3>
        <table class="table" id="existing-datasets-table">
            <thead>
                <tr>
                    <th><abbr title="DatasetName">Dataset Name</abbr></th>
                    <th><abbr title="Count">Count</abbr></th>
                    <th><abbr title="Actions">Actions</abbr></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th><abbr title="DatasetName">Dataset Name</abbr></th>
                    <th><abbr title="Count">Count</abbr></th>
                    <th><abbr title="Actions">Actions</abbr></th>
                </tr>
            </tfoot>
            <tbody>
                @foreach ($existing_datasets as $dataset)
                <tr>
                    <td>{{$dataset->dataset_name}}</td>
                    <td>{{$dataset->count}}</td>
                    <td>
                        <form action="/api/dataset" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="dataset-name" value="{{$dataset->dataset_name}}">
                            <button class="button is-link existing-dataset-delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

</body>

</html>
