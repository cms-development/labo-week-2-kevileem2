upvote = (e) => {
    const URL = "http://127.0.0.1:8000/api/vote";
    const id = e.target.parentNode.id
    const data = {"id": id,"vote": "up"}

    if(addToLikes(id)) {
        fetch(URL, {
            method: 'post',
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data)
        })
            .then(function (response) {
                const likes = parseInt(document.getElementById(id + "p").innerHTML) + 1
                document.getElementById(id + "p").innerHTML = `${likes} likes!`
                document.getElementById("likeButton").disabled = true
            })
            .catch(function (error) {
            });
    }
}

addToLikes = (id) => {
    let double = false
    if(window.localStorage.getItem('likes')) {
        let likes = JSON.parse(window.localStorage.getItem('likes'))
        likes.forEach((like) => {
            if(like === id) {
                double = true
            }
        })
        if(!double) {
            likes.push(id)
            window.localStorage.setItem('likes', JSON.stringify(likes))
            return true
        } else {
            return false
        }
    } else {
        let likes = new Array()
        likes.push(id)
        window.localStorage.setItem('likes', JSON.stringify(likes))
        return true
    }
}

checkIfLike = (id) => {
    if(window.localStorage.getItem('likes')) {
        let likes = JSON.parse(window.localStorage.getItem('likes'))
        likes.forEach((like, index) => {
            if (like === id) {
                likes.splice(index, 1)
                window.localStorage.setItem('likes', JSON.stringify(likes))
            }
        })
    }
}

document.addEventListener('DOMContentLoaded', function(event) {
    const likeButton = document.getElementById("likeButton")
    likeButton.addEventListener('click',upvote)
})