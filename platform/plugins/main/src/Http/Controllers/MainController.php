<?php

namespace Platform\Main\Http\Controllers;

use Platform\Base\Events\BeforeEditContentEvent;
use Platform\Main\Http\Requests\MainRequest;
use Platform\Main\Repositories\Interfaces\MainInterface;
use Platform\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Platform\Main\Tables\MainTable;
use Platform\Base\Events\CreatedContentEvent;
use Platform\Base\Events\DeletedContentEvent;
use Platform\Base\Events\UpdatedContentEvent;
use Platform\Base\Http\Responses\BaseHttpResponse;
use Platform\Main\Forms\MainForm;
use Platform\Base\Forms\FormBuilder;

class MainController extends BaseController
{
    /**
     * @var MainInterface
     */
    protected $mainRepository;

    /**
     * @param MainInterface $mainRepository
     */
    public function __construct(MainInterface $mainRepository)
    {
        $this->mainRepository = $mainRepository;
    }

    /**
     * @param MainTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MainTable $table)
    {
        page_title()->setTitle(trans('plugins/main::main.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/main::main.create'));

        return $formBuilder->create(MainForm::class)->renderForm();
    }

    /**
     * @param MainRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(MainRequest $request, BaseHttpResponse $response)
    {
        $main = $this->mainRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(MAIN_MODULE_SCREEN_NAME, $request, $main));

        return $response
            ->setPreviousUrl(route('main.index'))
            ->setNextUrl(route('main.edit', $main->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $main = $this->mainRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $main));

        page_title()->setTitle(trans('plugins/main::main.edit') . ' "' . $main->name . '"');

        return $formBuilder->create(MainForm::class, ['model' => $main])->renderForm();
    }

    /**
     * @param $id
     * @param MainRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, MainRequest $request, BaseHttpResponse $response)
    {
        $main = $this->mainRepository->findOrFail($id);

        $main->fill($request->input());

        $this->mainRepository->createOrUpdate($main);

        event(new UpdatedContentEvent(MAIN_MODULE_SCREEN_NAME, $request, $main));

        return $response
            ->setPreviousUrl(route('main.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $main = $this->mainRepository->findOrFail($id);

            $this->mainRepository->delete($main);

            event(new DeletedContentEvent(MAIN_MODULE_SCREEN_NAME, $request, $main));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $main = $this->mainRepository->findOrFail($id);
            $this->mainRepository->delete($main);
            event(new DeletedContentEvent(MAIN_MODULE_SCREEN_NAME, $request, $main));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
