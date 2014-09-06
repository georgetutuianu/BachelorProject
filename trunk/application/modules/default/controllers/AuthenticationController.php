<?php
/**
 * Description of AuthenticationController
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class AuthenticationController extends Utility_Controller_AbstractController
{
    public function loginAction()
    {
        $loginData = $this->getAllParams();
        
        $loginForm = new Form_LoginForm();
        if ($this->getRequest()->isPost() && $loginForm->isValid($loginData)) {
            $userModel = new Model_Users();
            $userEmail = $loginData['email'];
            $userPassword = $loginData['password'];
            if ($userModel->login($userEmail, $userPassword)) {
                $this->_redirect('/');
            }
        }
        
        $this->view->loginForm = $loginForm;
    }
    
    public function signupFormAction()
    {
        $signupForm = new Form_SignupForm();
        
        $this->view->signupForm = $signupForm;
    }
    
    public function signoutAction()
    {
        $userModel = new Model_Users();
        $userModel->signoutUser();
        
        $this->_redirect('/');
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }
    
    public function signupAction()
    {
        $this->_checkLoggedUser();
        
        $signupData = $this->getAllParams();
        
        $sigupForm = new Form_SignupForm();
        if ($sigupForm->isValid($signupData)) {
            $usersModel = new Model_Users();
            
            $userEmail = $signupData['Email'];
            $userPassword = $signupData['Password'];
            try {
                $usersModel->addUser($userEmail, $userPassword);
            } catch (Exception $exception) {
                
            }
            
        }
        $this->_redirect('/default/authentication/login-form');
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }
}
