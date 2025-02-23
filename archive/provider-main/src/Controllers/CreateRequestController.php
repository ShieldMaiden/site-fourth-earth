<?php

namespace FourthEarth\Site\Controllers;

use FourthEarth\Site\Controllers\AbstractController;

use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\Facades\Validator;

use Eightfold\ShoopShelf\Shoop;
use Eightfold\LaravelMarkup\UIKit;

use FourthEarth\Site\Models\Request;

class CreateRequestController extends AbstractController
{
    public function __invoke()
    {
        $request = request()->all();

        $rules = [
            "email"                  => "required|unique:requests|email",
            "disclosures"            => "required|min:2"
        ];

        $messages = [
            "email.required"       => "We need an email address.",
            "email.unique"         => "We already have this email address.",
            "email.email"          => "Does not appear to be an email address.",
            "disclosures.required" => "You must attest to being over 18 years of age and consent to receive emails.",
            "disclosures.min"      => "You must attest to being over 18 years of age and consent to receive emails."
        ];

        $validator = Validator::make($request, $rules, $messages);
        if ($validator->fails()) {
            parent::errorMessage();
            $validator->validate();
        }

        $request = Request::new($request["email"])->sendMail();

        session()->flash(
            "message",
            UIKit::doubleWrap(
                UIKit::h2("check your email"),
                UIKit::markdown("We have sent an email to <strong>". $request->email ."</strong> to verify your request.")
            )->outer("div", "is alert-success", "role alert")
        );
        return redirect("/");
    }
}
