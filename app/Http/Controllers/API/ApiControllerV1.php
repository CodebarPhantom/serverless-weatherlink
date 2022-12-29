<?php

namespace App\Http\Controllers\API;

use App\Exceptions\CustomException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class ApiControllerV1 extends Controller
{
    public function __construct()
    {
        $this->data = null;
        $this->messages = [];
        $this->error = false;
        $this->code = 200;
    }

    public function callFunction($func)
    {
        DB::beginTransaction();
        try {
            $func($this);
            if (!count($this->messages)) {
                array_push($this->messages, "Success");
            }

            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();

            $this->error = true;
            foreach ($e->errors() as $errors) {
                foreach ($errors as $key => $error) {
                    array_push($this->messages, $errors[$key]);
                }
            }
            $this->code = $e->status;
        } catch (QueryException $e) {
            DB::rollBack();
            report($e);

            $this->error = true;
            array_push($this->messages, $e->errorInfo[2]);
            $this->code = 500;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            $this->error = true;
            array_push($this->messages, $e->getMessage());
            $this->code = 404;
        } catch (CustomException $e) {
            DB::rollBack();

            $this->error = true;
            array_push($this->messages, $e->getMessage());
            $this->code = $e->getCode();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);

            $this->error = true;
            array_push($this->messages, $e->getMessage());
            $this->code = 500;
        }

        return response(
            [
                "data" => $this->data,
                "messages" => $this->messages,
                "error" => $this->error,
            ],
            $this->code
        );
    }
}
