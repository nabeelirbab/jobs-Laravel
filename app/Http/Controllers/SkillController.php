<?php

/**
 * Class SkillController.
 *
 * @category Worketic
 *
 * @package Worketic
 * @author  Amentotech <theamentotech@gmail.com>
 * @license http://www.amentotech.com Amentotech
 * @link    http://www.amentotech.com
 */

namespace App\Http\Controllers;

use App\Category;
use View;
use Auth;
use Session;
use App\Skill;
use App\User;
use App\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use DB;
use App\Helper;
use App\Cource;

/**
 * Class Skill Controller
 */
class SkillController extends Controller
{
    /**
     * Defining scope of the variable
     *
     * @access protected
     * @var    array $skill
     */
    protected $skill;

    /**
     * Create a new controller instance.
     *
     * @param instance $skill instance
     *
     * @return void
     */
    public function __construct(Skill $skill)
    {
        $this->skill = $skill;
    }

    /**
     * Display a listing of the resource.
     *
     * @param mixed $request Request Attributes
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->skill::with('category');

        // Search by keyword
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        // Filter by category if a category is selected
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('id', $request->category_id);
            });
        }

        $skills = $query->paginate(7)->appends($request->only(['keyword', 'category_id']));

        $categories = Category::all()->pluck('title', 'id');

        $viewPath = file_exists(resource_path('views/extend/back-end/admin/skills/index.blade.php'))
            ? 'extend.back-end.admin.skills.index'
            : 'back-end.admin.skills.index';

        return View::make($viewPath, compact('skills', 'categories'));
    }

    public function getcatskillsslug(Request $request)
    {
        // Check if category_id contains a comma
        if (strpos($request->category_id, ',') !== false) {
            // If comma is present, it's multiple IDs. Split them into an array.
            $categoryIds = explode(',', $request->category_id);
        } else {
            // If no comma, it's a single ID. Make it an array with one element.
            $categoryIds = [$request->category_id];
        }

        $query = Skill::with('category');

        $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('slug', $categoryIds); // Filter by one or multiple category IDs
        });

        $skills = $query->get();
        return $skills;
    }
    public function getcatskills(Request $request)
    {
        // Check if category_id contains a comma
        if (strpos($request->category_id, ',') !== false) {
            // If comma is present, it's multiple IDs. Split them into an array.
            $categoryIds = explode(',', $request->category_id);
        } else {
            // If no comma, it's a single ID. Make it an array with one element.
            $categoryIds = [$request->category_id];
        }

        $query = $this->skill::with('category');

        $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('title', $categoryIds); // Filter by one or multiple category IDs
        });

        $skills = $query->get();
        return $skills;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $request string
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'skill_logo' => 'required|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
        // ]);
        $request['categories'] =  $request->input('categories', []);
        if ($request->hasFile('skill_logo')) {
            $skill_logo = $request->file('skill_logo');
            $name = time() . '.' . $skill_logo->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/logos/');
            $skill_logo->move($destinationPath, $name);
            $request['logo'] = $name;
            $server_verification = Helper::worketicIsDemoSite();
            if (!empty($server_verification)) {
                Session::flash('error', $server_verification);
                return Redirect::back();
            }
            $this->validate(
                $request,
                [
                    'skill_title' => 'required',
                ]
            );
            $this->skill->saveSkills($request);
            Session::flash('message', trans('lang.save_skills'));
        } else {
            $server_verification = Helper::worketicIsDemoSite();
            if (!empty($server_verification)) {
                Session::flash('error', $server_verification);
                return Redirect::back();
            }
            $this->validate(
                $request,
                [
                    'skill_title' => 'required',
                ]
            );
            $this->skill->saveSkills($request);
            Session::flash('message', trans('lang.save_skills'));
        }

        return Redirect::back();
    }

    /**
     * Edit skills.
     *
     * @param int $id integer
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!empty($id)) {
            $skills = $this->skill::with('categories')->find($id);
            $categories = Category::all()->pluck('title', 'id');

            if (!empty($skills)) {
                if (file_exists(resource_path('views/extend/back-end/admin/skills/edit.blade.php'))) {
                    return View::make(
                        'extend.back-end.admin.skills.edit',
                        compact('id', 'skills', 'categories')
                    );
                } else {
                    return View::make(
                        'back-end.admin.skills.edit',
                        compact('id', 'skills', 'categories')
                    );
                }
                return Redirect::to('admin/skills');
            }
        }
    }

    /**
     * Update skills.
     *
     * @param string $request string
     * @param int    $id      integer
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request['categories'] =  $request->input('categories', []);

        if ($request->hasFile('skill_logo')) {

            // $this->validate($request, [
            //     'skill_logo' => 'required|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
            // ]);

            $skill_logo = $request->file('skill_logo');
            $name = time() . '.' . $skill_logo->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/logos/');
            $skill_logo->move($destinationPath, $name);
            $request['logo'] = $name;

            $server_verification = Helper::worketicIsDemoSite();
            if (!empty($server_verification)) {
                Session::flash('error', $server_verification);
                return Redirect::back();
            }
            $this->validate(
                $request,
                [
                    'skill_title' => 'required',
                ]
            );
            $this->skill->updateSkills($request, $id);
            Session::flash('message', trans('lang.skill_updated'));
            return Redirect::to('admin/skills');
        } else {
            $request['logo'] = $request->input('logo');
            $server_verification = Helper::worketicIsDemoSite();
            if (!empty($server_verification)) {
                Session::flash('error', $server_verification);
                return Redirect::back();
            }
            $this->validate(
                $request,
                [
                    'skill_title' => 'required',
                ]
            );
            $this->skill->updateSkills($request, $id);
            Session::flash('message', trans('lang.skill_updated'));
            return Redirect::to('admin/skills');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $request request attributes
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $json['type'] = 'error';
            $json['message'] = $server->getData()->message;
            return $json;
        }
        $json = array();
        $id = $request['id'];
        if (!empty($id)) {
            $this->skill::where('id', $id)->delete();
            $json['type'] = 'success';
            $json['message'] = trans('lang.skill_deleted');
            return $json;
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    /**
     * Get Freelancer Skills.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFreelancerSkills()
    {
        $json = array();
        $db_skills = Skill::select('id')->get()->pluck('id')->toArray();
        $freelancer_skills = Skill::getFreelancerSkill(Auth::user()->id);
        $result = array_diff($db_skills, $freelancer_skills);
        if (!empty($result)) {
            $skills = DB::table('skills')
                ->whereIn('id', $result)
                ->orderBy('title')->orderBy('title', 'asc')->get()->toArray();
        } else {
            $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
        }
        if (!empty($skills)) {
            $json['type'] = 'success';
            $json['skills'] = $skills;
            return $json;
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    public function getAdminFreelancerSkills(Request $request)
    {
        $agencyid = $request['slug'];
        $db_skills = Skill::select('id')->get()->pluck('id')->toArray();
        $user_skills = Skill::getFreelancerSkill($agencyid);
        if (!empty($agency_skills)) {
            $result = array_diff($db_skills, $user_skills);
            if (!empty($result)) {
                $skills = DB::table('skills')
                    ->whereIn('id', $result)
                    ->orderBy('title')->orderBy('title', 'asc')->get()->toArray();
            } else {
                $skills = array();
            }
            $json['type'] = 'success';
            $json['skills'] = $skills;
            $json['message'] = trans('lang.skills_already_selected');
            return $json;
        } else {
            $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
            if (!empty($skills)) {
                $json['type'] = 'success';
                $json['skills'] = $skills;
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        }
    }


    /**
     * Get Job Skills.
     *
     * @param mixed $request request attributes
     *
     * @return \Illuminate\Http\Response
     */
    public function getJobSkills(Request $request)
    {
        $json = array();
        if (!empty($request['slug']) && $request['slug'] != "post-job") {
            $job = Job::where('slug', $request['slug'])->select('id')->first();
            $db_skills = Skill::select('id')->get()->pluck('id')->toArray();
            $job_skills = Skill::getJobSkill($job->id);
            if (!empty($job_skills)) {
                $result = array_diff($db_skills, $job_skills);
                if (!empty($result)) {
                    $skills = DB::table('skills')
                        ->whereIn('id', $result)
                        ->orderBy('title')->orderBy('title', 'asc')->get()->toArray();
                } else {
                    $skills = array();
                }
                $json['type'] = 'success';
                $json['skills'] = $skills;
                $json['message'] = trans('lang.skills_already_selected');
                return $json;
            } else {
                $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
                if (!empty($skills)) {
                    $json['type'] = 'success';
                    $json['skills'] = $skills;
                    return $json;
                } else {
                    $json['type'] = 'error';
                    $json['message'] = trans('lang.something_wrong');
                    return $json;
                }
            }
        } else {
            $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
            if (!empty($skills)) {
                $json['type'] = 'success';
                $json['skills'] = $skills;
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        }
    }

    public function getCourseSkills(Request $request)
    { {
            $json = array();
            if (!empty($request['slug']) && $request['slug'] == "post-course") {

                $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
                if (!empty($skills)) {
                    $json['type'] = 'success';
                    $json['skills'] = $skills;
                    return $json;
                } else {
                    $json['type'] = 'error';
                    $json['message'] = trans('lang.something_wrong');
                    return $json;
                }
            } else {
                $agencyid = $request['slug'];
                $db_skills = Skill::select('id')->get()->pluck('id')->toArray();
                $agency_skills = Skill::getCourseSkill($agencyid);
                if (!empty($agency_skills)) {
                    $result = array_diff($db_skills, $agency_skills);
                    if (!empty($result)) {
                        $skills = DB::table('skills')
                            ->whereIn('id', $result)
                            ->orderBy('title')->orderBy('title', 'asc')->get()->toArray();
                    } else {
                        $skills = array();
                    }
                    $json['type'] = 'success';
                    $json['skills'] = $skills;
                    $json['message'] = trans('lang.skills_already_selected');
                    return $json;
                } else {
                    $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
                    if (!empty($skills)) {
                        $json['type'] = 'success';
                        $json['skills'] = $skills;
                        return $json;
                    } else {
                        $json['type'] = 'error';
                        $json['message'] = trans('lang.something_wrong');
                        return $json;
                    }
                }
            }
        }
    }
    public function getServiceSkills(Request $request)
    { {
            $json = array();
            if (!empty($request['slug']) && $request['slug'] == "post-service") {

                $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
                if (!empty($skills)) {
                    $json['type'] = 'success';
                    $json['skills'] = $skills;
                    return $json;
                } else {
                    $json['type'] = 'error';
                    $json['message'] = trans('lang.something_wrong');
                    return $json;
                }
            } else {
                $serviceid = $request['slug'];
                $db_skills = Skill::select('id')->get()->pluck('id')->toArray();
                $service_skills = Skill::getServiceSkill($serviceid);
                if (!empty($service_skills)) {
                    $result = array_diff($db_skills, $service_skills);
                    if (!empty($result)) {
                        $skills = DB::table('skills')
                            ->whereIn('id', $result)
                            ->orderBy('title')->orderBy('title', 'asc')->get()->toArray();
                    } else {
                        $skills = array();
                    }
                    $json['type'] = 'success';
                    $json['skills'] = $skills;
                    $json['message'] = trans('lang.skills_already_selected');
                    return $json;
                } else {
                    $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
                    if (!empty($skills)) {
                        $json['type'] = 'success';
                        $json['skills'] = $skills;
                        return $json;
                    } else {
                        $json['type'] = 'error';
                        $json['message'] = trans('lang.something_wrong');
                        return $json;
                    }
                }
            }
        }
    }
    public function getAgencySkills(Request $request)
    {
        $json = array();
        if (!empty($request['slug']) && $request['slug'] == "new") {
            $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
            if (!empty($skills)) {
                $json['type'] = 'success';
                $json['skills'] = $skills;
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        } else {
            $agencyid = $request['slug'];
            $db_skills = Skill::select('id')->get()->pluck('id')->toArray();
            $agency_skills = Skill::getAgencySkill($agencyid);
            if (!empty($agency_skills)) {
                $result = array_diff($db_skills, $agency_skills);
                if (!empty($result)) {
                    $skills = DB::table('skills')
                        ->whereIn('id', $result)
                        ->orderBy('title')->orderBy('title', 'asc')->get()->toArray();
                } else {
                    $skills = array();
                }
                $json['type'] = 'success';
                $json['skills'] = $skills;
                $json['message'] = trans('lang.skills_already_selected');
                return $json;
            } else {
                $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
                if (!empty($skills)) {
                    $json['type'] = 'success';
                    $json['skills'] = $skills;
                    return $json;
                } else {
                    $json['type'] = 'error';
                    $json['message'] = trans('lang.something_wrong');
                    return $json;
                }
            }
        }
    }
    public function getBlogSkills(Request $request)
    {
        $json = array();
        if (!empty($request['slug']) && $request['slug'] == "new") {
            $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
            if (!empty($skills)) {
                $json['type'] = 'success';
                $json['skills'] = $skills;
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        } else {
            $blogid = $request['slug'];
            $db_skills = Skill::select('id')->get()->pluck('id')->toArray();
            $blog_skills = Skill::getBlogSkill($blogid);
            if (!empty($blog_skills)) {
                $result = array_diff($db_skills, $blog_skills);
                if (!empty($result)) {
                    $skills = DB::table('skills')
                        ->whereIn('id', $result)
                        ->orderBy('title')->orderBy('title', 'asc')->get()->toArray();
                } else {
                    $skills = array();
                }
                $json['type'] = 'success';
                $json['skills'] = $skills;
                $json['message'] = trans('lang.skills_already_selected');
                return $json;
            } else {
                $skills = Skill::select('title', 'id')->orderBy('title', 'asc')->get()->toArray();
                if (!empty($skills)) {
                    $json['type'] = 'success';
                    $json['skills'] = $skills;
                    return $json;
                } else {
                    $json['type'] = 'error';
                    $json['message'] = trans('lang.something_wrong');
                    return $json;
                }
            }
        }
    }

    /**
     * Get Skills.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSkills()
    {
        $json = array();
        $skills = Skill::select('title', 'id')->get()->toArray();
        if (!empty($skills)) {
            $json['type'] = 'success';
            $json['skills'] = $skills;
            return $json;
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    public function getWhishlistSkills()
    {
        $json = array();
        $skills = Skill::all();
        if (!empty($skills)) {
            $json['type'] = 'success';
            $json['skills'] = $skills;
            return $json;
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $request request attributes
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteSelected(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $json['type'] = 'error';
            $json['message'] = $server->getData()->message;
            return $json;
        }
        $json = array();
        $checked = $request['ids'];
        foreach ($checked as $id) {
            $this->skill::where("id", $id)->delete();
        }
        if (!empty($checked)) {
            // $this->skill::whereIn($checked)->delete();
            $json['type'] = 'success';
            $json['message'] = trans('lang.skill_deleted');
            return $json;
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }
    public function getSkillsByCategory($categoryId)
    {
        if ($categoryId == 0) {
            $skills = Skill::get();
        } else {

            $skills = Skill::whereHas('categories', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            })->get();
        }
        return response()->json($skills);
    }
}
