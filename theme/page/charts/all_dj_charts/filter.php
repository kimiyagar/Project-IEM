<div class="col-md-12" id="charts-filter">
    <ul class="track_filter_list">
        <li>
            RELEASE DATE
            <div class="sub_track_filter" style="width: 350px">
                <form method="post" id="date-filter-tracks">
                    <i class="reset" style="cursor:pointer;position: absolute;top: 2px;right: 10px;bottom: 5px">Reset</i>
                    <input type="text" id="from" name="from" data-type="released" placeholder="start" />
                    <span>TO</span>
                    <input type="text" id="to" name="to" data-type="released" placeholder="end" />
                    <br>
                    <input type="submit" value="APPLY" class="apply_btn">
                </form>
            </div>
        </li>
        <li>GENRE
            <div id="genre" class="sub_track_filter">
                <i class="reset" style="cursor:pointer;position: absolute;top: 2px;right: 10px">Reset</i>
                <ul style="margin-left: 0px">
                        <li class="filter-genre-drop-list-item">
                            <input name="radioButton" value="" id="fg-genre-" data-type="genres" class="genre_input filter-drop-checkbox" type="checkbox" checked="checked">
                            <label for="fg-genre-" class="filter-drop-checkbox-label">
                            <span class="filter-item-count"></span></label>
                        </li>
                        <li class="filter-genre-drop-list-item">
                            <input name="radioButton" value="" id="fg-genre-" data-type="genres" class="filter-drop-checkbox" type="checkbox">
                            <label for="fg-genre-" class="filter-drop-checkbox-label">
                            <span class="filter-item-count"></span></label>
                        </li>
                </ul>
            </div>
        </li>
        <li>SUB GENRE
            <div id="genre" class="sub_track_filter">
                <i class="reset" style="cursor:pointer;position: absolute;top: 2px;right: 10px">Reset</i>
                <ul style="margin-left: 0px">
                    <li class="filter-genre-drop-list-item">
                        <input name="radioButton" value="" id="fg-genre-" data-type="genres" class="filter-drop-checkbox" type="checkbox">
                        <label for="fg-genre-" class="filter-drop-checkbox-label">
                        <span class="filter-item-count"></span></label>
                        </li>
                </ul>
            </div>
        </li>
    </ul>
</div>