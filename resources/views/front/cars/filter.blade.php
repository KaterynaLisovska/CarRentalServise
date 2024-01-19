<form class="formFilter flex flex-col mt-4 gap-5 rounded-sm ring-1 ring-zinc-700 mx-auto w-full">
    @csrf
    <h2 class="pl-4 bg-zinc-500/10 font-sans text-xl">Filters</h2>
    <div class="flex flex-row justify-between w-full">
        <div class="flex flex-col pl-4 pr-2 w-full">
            <label for="years" class="text-xl">Year</label>
            <select id="years" name="years" class="shadow-sm p-1 rounded-sm ring-1 ring-zinc-700">
                <option value="2023">2023</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
                <option value="2020">2020</option>
                <option value="2019">2019</option>
                <option value="2018">2018</option>
            </select>
        </div>
        <div class="flex flex-col pr-4 pl-2 w-full">
            <label for="months" class="text-xl">Months</label>
            <select id="months" name="months" class="shadow-sm p-1 rounded-sm ring-1 ring-zinc-700">
                @for($i = 1; $i < 13; $i++)
                    <option>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
    <button type="submit"
            class="mx-4 w-24 mb-5 flex flex-start justify-center rounded-md font-medium text-slate-700 shadow-sm ring-1 ring-slate-700/10 hover:bg-blue-1200">
        Show data
    </button>
</form>

<div id="preloader" class="hidden">
    <div>
        <em></em>
        <em></em>
        <em></em>
        <em></em>
    </div>
</div>
