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
                $this->_addFlashMessage(
                    'You have been succesfully logged in!',
                    BundlePhu_View_Helper_DisplayFlashMessages::SUCCESS_MESSAGE
                );
                
                $this->_redirect('/default/index/download-request');
            }
        }
        
        $this->view->loginForm = $loginForm;
    }
    
    public function signoutAction()
    {
        $this->_checkLoggedUser();
        
        $userModel = new Model_Users();
        $userModel->signoutUser();
        
        $this->_redirect('/');
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }
    
    public function signupAction()
    {
        $signupData = $this->getAllParams();
        
        $signupForm = new Form_SignupForm();
        if ($this->getRequest()->isPost() && $signupForm->isValid($signupData)) {
            $usersModel = new Model_Users();
            
            $userEmail = $signupData['Email'];
            $userPassword = $signupData['Password'];
            try {
                $usersModel->addUser($userEmail, $userPassword);
                $this->_addFlashMessage(
                    'The user have been successfully added. You can nou login!',
                    BundlePhu_View_Helper_DisplayFlashMessages::SUCCESS_MESSAGE
                );
                $this->_redirect('/default/authentication/login');
            } catch (Exception $exception) {
                $this->_addFlashMessage(
                    'An error occurred. Please try again!',
                    BundlePhu_View_Helper_DisplayFlashMessages::ERROR_MESSAGE
                );
            }
        }
        
        $this->view->signupForm = $signupForm;
    }
}
