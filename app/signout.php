<?php
session_start();
unset($_SESSION['signin']);
header('Location: ./');