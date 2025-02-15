<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\EmailHelper;
use App\SiteManagement;
use Auth;

class FreelancerEmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Setting scope of the variables
     *
     * @access public
     *
     * @var string $type
     *
     * @var collection $template
     *
     * @var array $email_params
     *
     */
    public $type;
    public $template;
    public $email_params;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($type, $template, $email_params = array())
    {
        $this->type = $type;
        $this->template = $template;
        $this->email_params = $email_params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from_email = EmailHelper::getEmailFrom();
        $from_email_id = EmailHelper::getEmailID();
        $subject = !empty($this->template->subject) ? $this->template->subject : '';
        if ($this->type == 'freelancer_email_new_proposal_submitted') {
            $email_message = $this->prepareFreelancerEmailPropsalSubmitted($this->email_params);
        } elseif ($this->type == 'freelancer_email_hire_freelancer') {
            $email_message = $this->prepareFreelancerEmailFreelancerHired($this->email_params);
        } elseif ($this->type == 'freelancer_email_send_offer') {
            $email_message = $this->prepareFreelancerEmailSendOffer($this->email_params);
        } elseif ($this->type == 'freelancer_email_cancel_job') {
            $email_message = $this->prepareFreelancerEmailJobCancelled($this->email_params);
        } elseif ($this->type == 'freelancer_email_proposal_message') {
            $email_message = $this->prepareFreelancerEmailProposalMessage($this->email_params);
        } elseif ($this->type == 'freelancer_email_package_subscribed') {
            $email_message = $this->prepareFreelancerEmailPackagePurchased($this->email_params);
        } elseif ($this->type == 'freelancer_email_job_completed') {
            $email_message = $this->prepareFreelancerEmailJobCompleted($this->email_params);
        } elseif ($this->type == 'freelancer_email_new_order') {
            $email_message = $this->prepareFreelancerEmailNewOrder($this->email_params);
        }elseif ($this->type == 'freelancer_email_new_course_posted') {
            $email_message = $this->prepareFreelancerEmailCoursePosted($this->email_params);
        }elseif ($this->type == 'freelancer_email_new_course_order') {
            $email_message = $this->prepareFreelancerEmailNewCourseOrder($this->email_params);
        }elseif ($this->type == 'freelancer_email_course_enrolled') {
            $email_message = $this->prepareFreelancerEmailCourseEnrolled($this->email_params);
        }elseif ($this->type == 'new_agency') {
            $email_message = $this->prepareFreelancerEmailNewAgency($this->email_params);
        } elseif ($this->type == 'join_agency') {
            $email_message = $this->prepareFreelancerEmailJoinAgency($this->email_params);
        }elseif ($this->type == 'accept_agency') {
            $email_message = $this->getAgencyInvitationAcceptEmailContent($this->email_params);
        }
        elseif ($this->type == 'decline_agency') {
            $email_message = $this->getAgencyInvitationDeclineEmailContent($this->email_params);
        } 
        elseif ($this->type == 'agency_invitation') {
            $email_message = $this->getAgencyInvitationEmailContent($this->email_params);
        } 
        elseif ($this->type == 'freelancer_email_course_cancelled') {
            $email_message = $this->prepareFreelancerEmailCourseCancel($this->email_params);
        }
        $message = $this->from($from_email, $from_email_id)
            ->subject($subject)->view('emails.index')
            ->with(
                [
                    'html' => $email_message,
                ]
            );
        return $message;
    }

    /**
     * Proposal submitted
     *
     * @param array $email_params Email Parameters
     *
     * @access public
     *
     * @return string
     */
    public function prepareFreelancerEmailPropsalSubmitted($email_params)
    {
        extract($email_params);
        $freelancer_name = $freelancer;
        $freelancer_link = $freelancer_profile;
        $project_title = $title;
        $project_link = $link;
        $proposal_amount = $amount;
        $proposal_duration = $duration;
        $proposal_message = $message;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello <a href='%freelancer_link%'>%freelancer_name%</a>,

                                    You have submitted the proposal against this job <a href='%project_link%'>%project_title%</a>.
                                    Message is given below.
                                    Project Proposal Amount : %proposal_amount%
                                    Project Duration : %proposal_duration%
                                    Message: %message%

                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%project_link%", $project_link, $app_content);
        $app_content = str_replace("%project_title%", $project_title, $app_content);
        $app_content = str_replace("%proposal_amount%", $proposal_amount, $app_content);
        $app_content = str_replace("%proposal_duration%", $proposal_duration, $app_content);
        $app_content = str_replace("%message%", $proposal_message, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    /**
     * Email freelancer hired
     *
     * @param array $email_params Email Parameters
     *
     * @access public
     *
     * @return string
     */
    public function prepareFreelancerEmailFreelancerHired($email_params)
    {
        extract($email_params);
        $title = $project_title;
        $project_link = $hired_project_link;
        $freelancer_name = $name;
        $freelancer_link = $link;
        $employer_link = $employer_profile;
        $employer_name = $emp_name;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello <a href='%freelancer_link%'>%freelancer_name%</a>,

                                    The <a href='%employer_link%'>%employer_name%</a> hired you for the following job <a href='%project_link%'>%project_title%</a>.

                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%project_link%", $project_link, $app_content);
        $app_content = str_replace("%project_title%", $title, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }
     
    /**
     * Email send offer
     *
     * @param array $email_params Email Parameters
     *
     * @access public
     *
     * @return string
     */
    public function prepareFreelancerEmailSendOffer($email_params)
    {
        extract($email_params);
        $title = $project_title;
        $project_link = $project_link;
        $freelancer_name = $name;
        $freelancer_link = $link;
        $employer_link = $employer_profile;
        $employer_name = $emp_name;
        $message = $msg;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hi <a href='%freelancer_link%'>%freelancer_name%</a>,

                                    The <a href='%employer_link%'>%employer_name%</a> would like to invite you to consider working on the following project <a href='%project_link%'>%project_title%</a>
                                    Message: %message%
                                    
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%project_link%", $project_link, $app_content);
        $app_content = str_replace("%project_title%", $title, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%message%", $message, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    /**
     * Email job cancelled
     * 
     * @param array $email_params Email Parameters
     * 
     * @access public
     *
     * @return string
     */
    public function prepareFreelancerEmailJobCancelled($email_params)
    {
        extract($email_params);
        $title = $project_title;
        $project_link = $cancelled_project_link;
        $freelancer_name = $name;
        $freelancer_link = $link;
        $employer_link = $employer_profile;
        $employer_name = $emp_name;
        $message = $msg;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello <a href='%freelancer_link%'>%freelancer_name%</a>,

                                    Unfortunately <a href=' %employer_link%'>%employer_name%</a> cancelled the <a href='%project_link%'>%project_title%</a> project due to following below reasons.
                                    Job Cancel Reasons Below.
                                    Message: %message%
                                    
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%project_link%", $project_link, $app_content);
        $app_content = str_replace("%project_title%", $title, $app_content);
        $app_content = str_replace("%message%", $message, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    /**
     * Proposal message
     *
     * @param array $email_params Email Parameters
     *
     * @access public
     *
     * @return string
     */
    public function prepareFreelancerEmailProposalMessage($email_params)
    {
        extract($email_params);
        $employer_name = $employer;
        $employer_link = $employer_profile;
        $freelancer_name = $freelancer;
        $freelancer_link = $freelancer_profile;
        $project_title = $title;
        $project_link = $link;
        $proposal_message = $message;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello <a href='%freelancer_link%'>%freelancer_name%</a>,

                                    The <a href='%employer_link%'>%employer_name%</a> submit the proposal message on this job <a href='%project_link%'>%project_title%</a>.
                                    Login to view your proposal message.
                                    Message: %message%
                                    
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%project_link%", $project_link, $app_content);
        $app_content = str_replace("%project_title%", $project_title, $app_content);
        $app_content = str_replace("%message%", $proposal_message, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    /**
     * Package Purchased
     *
     * @param array $email_params Email Parameters
     *
     * @access public
     *
     * @return string
     */
    public function prepareFreelancerEmailPackagePurchased($email_params)
    {
        extract($email_params);
        $freelancer_name = $freelancer;
        $freelancer_link = $freelancer_profile;
        $package_name = $name;
        $package_price = $price;
        $package_expiry = $expiry_date;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello <a href='%freelancer_link%'>%freelancer_name%</a>,

                                    You have subscribe to the following %package_name% at cost of %package_price% which will be expire on %package_expiry%.
                                    
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%package_name%", $package_name, $app_content);
        $app_content = str_replace("%package_price%", $package_price, $app_content);
        $app_content = str_replace("%package_expiry%", $package_expiry, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    /**
     * Email job completed 
     * 
     * @param array $email_params Email Parameters
     * 
     * @access public
     *
     * @return string
     */
    public function prepareFreelancerEmailJobCompleted($email_params)
    {
        extract($email_params);
        $employer_name = $employer;
        $employer_link = $employer_profile;
        $title = $project_title;
        $project_link = $completed_project_link;
        $freelancer_name = $name;
        $freelancer_link = $link;
        $rating = $ratings;
        $message = $review;
        $signature = EmailHelper::getSignature();
        $app_content = !empty($this->template->content) ?  $this->template->content : $this->template;
        $email_content_default =    "Hello <a href='%freelancer_link%'>%freelancer_name%</a>,

                                    The <a href='%employer_link%'>%employer_name%</a> is confirmed that the following project (<a href='%project_link%'>%project_title%</a>) is completed.
                                    You have received the following ratings %rating% from employer.
                                    Message: %message%
                                    Ratings: %ratings%
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%project_link%", $project_link, $app_content);
        $app_content = str_replace("%project_title%", $title, $app_content);
        $app_content = str_replace("%rating%", $rating, $app_content);
        $app_content = str_replace("%message%", $message, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    /**
     * Proposal submitted
     *
     * @param array $email_params Email Parameters
     *
     * @access public
     *
     * @return string
     */
    public function prepareFreelancerEmailNewOrder($email_params)
    {
        extract($email_params);
        $employer_name = $employer_name;
        $freelancer_name = $freelancer_name;
        $employer_link = $employer_profile;
        $service_title = $title;
        $service_link = $service_link;
        $service_amount = $amount;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template;
        $email_content_default =    "Hello %freelancer_name%,

                                    <a href='%employer_link%'>%employer_name%</a> has purchased your following service <a href='%service_link%'>%service_title%</a>.
                                    Service amount is %service_amount%
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%service_link%", $service_link, $app_content);
        $app_content = str_replace("%service_title%", $service_title, $app_content);
        $app_content = str_replace("%service_amount%", $service_amount, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    public function prepareFreelancerEmailNewCourseOrder($email_params)
    {
        extract($email_params);
        $employer_name = $employer_name;
        $freelancer_name = $freelancer_name;
        $employer_link = $employer_profile;
        $course_title = $title;
        $course_link = $course_link;
        $course_amount = $amount;
        $payment_mode = $payment_mode;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello %freelancer_name%,

                                    <a href='%employer_link%'>%employer_name%</a> has purchased your following course <a href='%course_link%'>%course_title%</a> through %payment_mode%.
                                    course amount is %course_amount%. the student is waiting for you to enroll
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%course_link%", $course_link, $app_content);
        $app_content = str_replace("%course_title%", $course_title, $app_content);
        $app_content = str_replace("%course_amount%", $course_amount, $app_content);
        $app_content = str_replace("%payment_mode%", $payment_mode, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    public function prepareFreelancerEmailCourseEnrolled($email_params)
    {
        extract($email_params);
        $employer_name = $employer_name;
        $freelancer_name = $freelancer_name;
        $employer_link = $employer_profile;
        $course_title = $title;
        $course_link = $course_link;
        $course_amount = $amount;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello %freelancer_name%,

                                    <a href='%employer_link%'>%employer_name%</a> has enrolled you for the following course <a href='%course_link%'>%course_title%</a>.
                                    course amount is %course_amount%. 
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%course_link%", $course_link, $app_content);
        $app_content = str_replace("%course_title%", $course_title, $app_content);
        $app_content = str_replace("%course_amount%", $course_amount, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }

    public function prepareFreelancerEmailCourseCancel($email_params)
    {
        extract($email_params);
        $employer_name = $employer_name;
        $freelancer_name = $freelancer_name;
        $employer_link = $employer_profile;
        $course_title = $title;
        $course_link = $course_link;
        $course_amount = $amount;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello %freelancer_name%,

                                    <a href='%employer_link%'>%employer_name%</a> has not enrolled you for the following course <a href='%course_link%'>%course_title%</a>.
                                    ,Your course Amount has been refunded! course amount is %course_amount%. 
                                    %signature%,";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%employer_name%", $employer_name, $app_content);
        $app_content = str_replace("%employer_link%", $employer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%course_link%", $course_link, $app_content);
        $app_content = str_replace("%course_title%", $course_title, $app_content);
        $app_content = str_replace("%course_amount%", $course_amount, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }



    public function prepareFreelancerEmailNewAgency($email_params){
		extract($email_params);
        $freelancer_name = $freelancer_name;
		$freelancer_link = 'http://dev.ebelong.com/profile/'.Auth::user()->slug;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default = 	'<p>Hello <strong><a href="%freelancer_link%">%freelancer_name%</a></strong>,</p>
									<p>Your agency has been added successfully.</p>
									<p>%signature%</p>';
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
	}
	
	public function prepareFreelancerEmailJoinAgency($email_params){
		extract($email_params);
        $freelancer_name = $agency_creator_name;
        $agency_name = $agency_name;
		$freelancer_link = 'http://dev.ebelong.com/profile/'.Auth::user()->slug;
        $member_link = $agency_member_link;
        $member_name = $agency_member_name;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default = 	'<p>Hello <strong><a href="%freelancer_link%">%freelancer_name%</a></strong>,</p>
									 <p>Your %agency_name% join request sent successfully to <strong><a href="%member_link%">%member_name%</a></strong>.</p>
									 <p>%signature%</p>';
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        } 
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%agency_name%", $agency_name, $app_content);
        $app_content = str_replace("%member_name%", $member_name, $app_content);
        $app_content = str_replace("%member_link%", $member_link, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
	}
    public function getAgencyInvitationAcceptEmailContent($email_params){
		extract($email_params);
        $creator_name = $agency_creator_name;
        $member_name = $agency_member_name;
        $agency_name = $agency_name;
		$member_link = 'http://dev.ebelong.com/profile/'.Auth::user()->slug;
        $signature = EmailHelper::getSignature();
        // /dd($this->template);
        $app_content = $this->template->content;
        $email_content_default = 	'<p>Hello <strong><a href="%creator_link%">%creator_name%</a></strong>,</p>
									 <p>Your Invitation request is accepted successfully by <strong><a href="%member_link%">%member_name%</a></strong> for %agency_name%.</p>
									 <p>%signature%</p>';
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        } 
        $app_content = str_replace("%creator_name%", $creator_name, $app_content);
        $app_content = str_replace("%member_link%", $member_link, $app_content);
        $app_content = str_replace("%member_name%", $member_name, $app_content);
        $app_content = str_replace("%agency_name%", $agency_name, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
	}
    public function getAgencyInvitationDeclineEmailContent($email_params){
		extract($email_params);
        $creator_name = $agency_creator_name;
        $member_name = $agency_member_name;
        $agency_name = $agency_name;
		$member_link = 'http://dev.ebelong.com/profile/'.Auth::user()->slug;
        $signature = EmailHelper::getSignature();
        // /dd($this->template);
        $app_content = $this->template->content;
        $email_content_default = 	'<p>Hello <strong><a href="%creator_link%">%creator_name%</a></strong>,</p>
									 <p>Your Invitation request is declined by <strong><a href="%member_link%">%member_name%</a></strong> for %agency_name%.</p>
									 <p>%signature%</p>';
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        } 
        $app_content = str_replace("%creator_name%", $creator_name, $app_content);
        $app_content = str_replace("%member_link%", $member_link, $app_content);
        $app_content = str_replace("%member_name%", $member_name, $app_content);
        $app_content = str_replace("%agency_name%", $agency_name, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
	}
    public function getAgencyInvitationEmailContent($email_params){
		extract($email_params);
        $creator_name = $agency_creator_name;
        $member_name = $agency_member_name;
        $agency_name = $agency_name;
		$creator_link = 'http://dev.ebelong.com/profile/'.Auth::user()->slug;
        $signature = EmailHelper::getSignature();
        // /dd($this->template);
        $app_content = $this->template->content;
        $email_content_default = 	'<p>Hello %member_name%</a></strong>,</p>
									 <p>You are Invited by <strong><a href="%creator_link%">%creator_name%</a></strong> for %agency_name%.</p>
									 <p>%signature%</p>';
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        } 
        $app_content = str_replace("%creator_name%", $creator_name, $app_content);
        $app_content = str_replace("%member_link%", $creator_link, $app_content);
        $app_content = str_replace("%member_name%", $member_name, $app_content);
        $app_content = str_replace("%agency_name%", $agency_name, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
	}
    public function prepareFreelancerEmailCoursePosted($email_params)
    {
        extract($email_params);
        $title = $cource_title;
        $cource_link = $posted_cource_link;
        $cource_name = $name;
        $freelancer_name = $name;
        $freelancer_link = $link;
        $signature = EmailHelper::getSignature();
        $app_content = $this->template->content;
        $email_content_default =    "Hello  <a href='%freelancer_link%'> <br>,
                                    You have posted a new course.
                                    Click to view the course link. <a href='%cource_link%' target='_blank' rel='noopener'>%cource_title%</a>

                                    %signature%";
        //set default contents
        if (empty($app_content)) {
            $app_content = $email_content_default;
        }
        $app_content = str_replace("%freelancer_link%", $freelancer_link, $app_content);
        $app_content = str_replace("%freelancer_name%", $freelancer_name, $app_content);
        $app_content = str_replace("%cource_link%", $cource_link, $app_content);
        $app_content = str_replace("%cource_title%", $title, $app_content);
        $app_content = str_replace("%signature%", $signature, $app_content);

        $body = "";
        $body .= EmailHelper::getEmailHeader();
        $body .= $app_content;
        $body .= EmailHelper::getEmailFooter();
        return $body;
    }
}
